<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_site_root_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_active_admin_is_redirected_from_site_root_to_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/');

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_guest_is_redirected_from_admin_home_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_guest_is_redirected_from_admin_profile_to_login(): void
    {
        $response = $this->get('/admin/profile');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_marketing_user_receives_forbidden_on_admin_profile(): void
    {
        $marketing = User::factory()->marketing()->create();

        $response = $this->actingAs($marketing)->get('/admin/profile');

        $response->assertForbidden();
        $response->assertSee('Akses tidak tersedia.');
    }

    public function test_marketing_user_receives_forbidden_on_admin_home(): void
    {
        $marketing = User::factory()->marketing()->create();

        $response = $this->actingAs($marketing)->get('/admin');

        $response->assertForbidden();
        $response->assertSee('Akses tidak tersedia.');
        $response->assertSee('Keluar');
        $response->assertDontSee('Halaman ini hanya untuk administrator.');
    }

    public function test_inactive_admin_is_logged_out_from_admin_home(): void
    {
        $inactiveAdmin = User::factory()->admin()->inactive()->create();

        $response = $this->actingAs($inactiveAdmin)->get('/admin');

        $response->assertRedirect(route('login', absolute: false));
        $this->assertGuest();
    }

    public function test_active_admin_can_access_admin_home(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin MMS',
            'username' => 'admin.mms',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('Selamat bekerja, Admin MMS');
        $response->assertSee('admin.mms');
    }

    public function test_health_endpoint_stays_public(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk();
    }

    public function test_inactive_json_request_receives_json_error(): void
    {
        $inactiveAdmin = User::factory()->admin()->inactive()->create();

        $response = $this->actingAs($inactiveAdmin)->getJson('/admin');

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Akses tidak tersedia.',
        ]);
    }
}
