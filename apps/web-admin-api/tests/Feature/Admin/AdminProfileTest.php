<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_profile_page(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin MMS',
            'username' => 'admin.mms',
        ]);

        $response = $this->actingAs($admin)->get('/admin/profile');

        $response->assertOk();
        $response->assertSee('Profil Admin');
        $response->assertSee('Admin MMS');
        $response->assertSee('admin.mms');
        $response->assertSee('Status Akun');
        $response->assertSee('Login Terakhir');
        $response->assertSee('Tampilkan password saat ini');
        $response->assertSee('aria-pressed', false);
    }

    public function test_admin_can_update_name_and_email(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'old.admin@example.test',
        ]);

        $response = $this->actingAs($admin)->put('/admin/profile', [
            'name' => 'Admin Baru',
            'email' => 'new.admin@example.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'profile-updated');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Baru',
            'email' => 'new.admin@example.test',
        ]);
    }

    public function test_profile_update_does_not_change_protected_account_fields(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin.mms',
            'email' => 'admin@example.test',
            'password' => Hash::make('OriginalPassword123!'),
            'last_login_at' => now()->subDay(),
        ]);

        $originalLastLoginAt = $admin->last_login_at;

        $response = $this->actingAs($admin)->put('/admin/profile', [
            'name' => 'Admin Aman',
            'email' => 'admin.updated@example.test',
            'username' => 'changed.username',
            'role' => UserRole::Marketing->value,
            'is_active' => false,
            'password' => 'ChangedPassword123!',
            'last_login_at' => now()->addDay()->toDateTimeString(),
        ]);

        $response->assertRedirect();

        $admin->refresh();

        $this->assertSame('Admin Aman', $admin->name);
        $this->assertSame('admin.updated@example.test', $admin->email);
        $this->assertSame('admin.mms', $admin->username);
        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(Hash::check('OriginalPassword123!', $admin->password));
        $this->assertTrue($admin->last_login_at->equalTo($originalLastLoginAt));
    }

    public function test_profile_email_must_be_unique_when_present(): void
    {
        User::factory()->admin()->create([
            'email' => 'taken@example.test',
        ]);

        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.test',
        ]);

        $response = $this->actingAs($admin)->from('/admin/profile')->put('/admin/profile', [
            'name' => 'Admin MMS',
            'email' => 'taken@example.test',
        ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHasErrors('email');
    }

    public function test_profile_email_must_be_valid(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->from('/admin/profile')->put('/admin/profile', [
            'name' => 'Admin MMS',
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_keep_own_email_and_email_is_nullable(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.test',
        ]);

        $this->actingAs($admin)->put('/admin/profile', [
            'name' => 'Admin MMS',
            'email' => 'admin@example.test',
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->put('/admin/profile', [
            'name' => 'Admin MMS',
            'email' => null,
        ])->assertSessionHasNoErrors();

        $this->assertNull($admin->fresh()->email);
    }

    public function test_guest_and_marketing_cannot_update_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = User::factory()->marketing()->create();

        $this->put('/admin/profile', [
            'name' => 'Guest Update',
        ])->assertRedirect(route('login', absolute: false));

        $this->actingAs($marketing)->put('/admin/profile', [
            'name' => 'Marketing Update',
        ])->assertForbidden();

        $this->assertNotSame('Guest Update', $admin->fresh()->name);
    }
}
