<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_password(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('OriginalPassword123!'),
        ]);

        $response = $this->actingAs($admin)->from('/admin/profile')->put('/admin/password', [
            'current_password' => 'OriginalPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('NewPassword123!', $admin->fresh()->password));
        $this->assertAuthenticated();
    }

    public function test_password_form_has_visibility_controls(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/profile');

        $response->assertOk();
        $response->assertSee('Password Saat Ini');
        $response->assertSee('Password Baru');
        $response->assertSee('Konfirmasi Password Baru');
        $response->assertSee('Tampilkan password saat ini');
        $response->assertSee('Sembunyikan password baru');
        $response->assertSee('aria-pressed', false);
        $response->assertSee('x-bind:disabled', false);
        $response->assertSee('aria-busy', false);
    }

    public function test_guest_and_marketing_cannot_update_password(): void
    {
        $marketing = User::factory()->marketing()->create();

        $this->put('/admin/password', [
            'current_password' => 'OriginalPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('login', absolute: false));

        $this->actingAs($marketing)->put('/admin/password', [
            'current_password' => 'OriginalPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertForbidden();
    }

    public function test_password_route_only_accepts_put_and_preserves_account_fields(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('OriginalPassword123!'),
        ]);

        $this->actingAs($admin)->post('/admin/password', [
            'current_password' => 'OriginalPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertStatus(405);

        $this->actingAs($admin)->from('/admin/profile')->put('/admin/password', [
            'current_password' => 'OriginalPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
            'username' => 'changed.username',
            'role' => 'marketing',
            'is_active' => false,
        ])->assertSessionHas('status', 'password-updated');

        $admin->refresh();

        $this->assertSame('admin.mms', $admin->username);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->is_active);
        $this->assertAuthenticatedAs($admin);
    }

    public function test_current_password_must_be_valid(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('OriginalPassword123!'),
        ]);

        $response = $this->actingAs($admin)->from('/admin/profile')->put('/admin/password', [
            'current_password' => 'WrongPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('OriginalPassword123!', $admin->fresh()->password));
    }

    public function test_new_password_must_be_confirmed(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('OriginalPassword123!'),
        ]);

        $response = $this->actingAs($admin)->from('/admin/profile')->put('/admin/password', [
            'current_password' => 'OriginalPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('OriginalPassword123!', $admin->fresh()->password));
    }

    public function test_new_password_must_be_at_least_eight_characters(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('OriginalPassword123!'),
        ]);

        $response = $this->actingAs($admin)->from('/admin/profile')->put('/admin/password', [
            'current_password' => 'OriginalPassword123!',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('OriginalPassword123!', $admin->fresh()->password));
    }

    public function test_new_password_must_be_different_from_current_password(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => Hash::make('OriginalPassword123!'),
        ]);

        $response = $this->actingAs($admin)->from('/admin/profile')->put('/admin/password', [
            'current_password' => 'OriginalPassword123!',
            'password' => 'OriginalPassword123!',
            'password_confirmation' => 'OriginalPassword123!',
        ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('OriginalPassword123!', $admin->fresh()->password));
    }
}
