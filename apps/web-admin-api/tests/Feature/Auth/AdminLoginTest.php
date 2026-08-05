<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Akses Admin');
        $response->assertSee('Masuk');
        $response->assertSee('Tampilkan password');
        $response->assertSee('aria-pressed', false);
        $response->assertSee('Memproses');
        $response->assertDontSee('Daftar');
        $response->assertDontSee('Lupa password');
    }

    public function test_active_admin_can_login_with_username_and_password(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('CorrectPassword123!'),
            'last_login_at' => null,
        ]);

        $response = $this->post('/login', [
            'username' => 'admin.mms',
            'password' => 'CorrectPassword123!',
        ]);

        $response->assertRedirect(route('admin.home', absolute: false));
        $response->assertSessionHas('flash_message', 'Login berhasil.');
        $this->assertAuthenticatedAs($admin);
        $this->assertNotNull($admin->fresh()->last_login_at);
    }

    public function test_admin_login_normalizes_username_without_changing_password(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('  Correct Password  '),
        ]);

        $response = $this->post('/login', [
            'username' => '  ADMIN.MMS  ',
            'password' => '  Correct Password  ',
        ]);

        $response->assertRedirect(route('admin.home', absolute: false));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_login_does_not_trim_password(): void
    {
        User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('  Correct Password  '),
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'admin.mms',
            'password' => 'Correct Password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_admin_login_rejects_non_scalar_username_without_error(): void
    {
        $response = $this->from('/login')->post('/login', [
            'username' => ['admin.mms'],
            'password' => 'CorrectPassword123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_invalid_credentials_use_generic_error_and_do_not_flash_password(): void
    {
        User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'admin.mms',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'username' => 'Username atau password tidak sesuai.',
        ]);
        $response->assertSessionHas('_old_input.username', 'admin.mms');
        $response->assertSessionMissing('_old_input.password');
        $this->assertGuest();
    }

    public function test_marketing_user_cannot_login_to_web_admin(): void
    {
        User::factory()->marketing()->create([
            'username' => 'm01.deden',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'username' => 'Username atau password tidak sesuai.',
        ]);
        $this->assertGuest();
    }

    public function test_inactive_admin_cannot_login_to_web_admin(): void
    {
        User::factory()->admin()->inactive()->create([
            'username' => 'inactive.admin',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'inactive.admin',
            'password' => 'CorrectPassword123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'username' => 'Username atau password tidak sesuai.',
        ]);
        $this->assertGuest();
    }

    public function test_authenticated_admin_is_redirected_away_from_login_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect(route('admin.home', absolute: false));
    }

    public function test_login_rate_limit_uses_username_and_ip(): void
    {
        config([
            'mms.auth.admin_login_max_attempts' => 2,
            'mms.auth.admin_login_decay_seconds' => 30,
        ]);

        RateLimiter::clear('admin.mms|127.0.0.1');

        User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->from('/login')->post('/login', [
                'username' => 'admin.mms',
                'password' => 'WrongPassword123!',
            ])->assertSessionHasErrors('username');
        }

        $response = $this->from('/login')->post('/login', [
            'username' => 'admin.mms',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_rate_limit_uses_normalized_username(): void
    {
        config([
            'mms.auth.admin_login_max_attempts' => 1,
            'mms.auth.admin_login_decay_seconds' => 30,
        ]);

        RateLimiter::clear('admin.mms|127.0.0.1');

        User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $this->from('/login')->post('/login', [
            'username' => '  ADMIN.MMS  ',
            'password' => 'WrongPassword123!',
        ])->assertSessionHasErrors('username');

        $this->assertTrue(RateLimiter::tooManyAttempts('admin.mms|127.0.0.1', 1));
        $this->assertFalse(RateLimiter::tooManyAttempts('  ADMIN.MMS  |127.0.0.1', 1));
    }

    public function test_different_username_and_ip_do_not_share_login_lock(): void
    {
        config([
            'mms.auth.admin_login_max_attempts' => 1,
            'mms.auth.admin_login_decay_seconds' => 30,
        ]);

        User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $this->from('/login')->post('/login', [
            'username' => 'admin.mms',
            'password' => 'WrongPassword123!',
        ])->assertSessionHasErrors('username');

        $this->from('/login')->post('/login', [
            'username' => 'other.admin',
            'password' => 'WrongPassword123!',
        ])->assertSessionHasErrors([
            'username' => 'Username atau password tidak sesuai.',
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.10'])
            ->from('/login')
            ->post('/login', [
                'username' => 'admin.mms',
                'password' => 'WrongPassword123!',
            ])->assertSessionHasErrors([
                'username' => 'Username atau password tidak sesuai.',
            ]);
    }

    public function test_successful_login_clears_rate_limiter(): void
    {
        config([
            'mms.auth.admin_login_max_attempts' => 2,
            'mms.auth.admin_login_decay_seconds' => 30,
        ]);

        RateLimiter::clear('admin.mms|127.0.0.1');

        User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $this->from('/login')->post('/login', [
            'username' => 'admin.mms',
            'password' => 'WrongPassword123!',
        ])->assertSessionHasErrors('username');

        $this->post('/login', [
            'username' => 'admin.mms',
            'password' => 'CorrectPassword123!',
        ])->assertRedirect(route('admin.home', absolute: false));

        $this->assertFalse(RateLimiter::tooManyAttempts('admin.mms|127.0.0.1', 2));
    }
}
