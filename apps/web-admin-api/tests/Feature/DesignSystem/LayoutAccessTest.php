<?php

declare(strict_types=1);

namespace Tests\Feature\DesignSystem;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_layout_renders_sidebar_topbar_and_mobile_navigation(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin MMS',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('Navigasi admin');
        $response->assertSee('Navigasi bawah');
        $response->assertSee('LOGO-KSP.jpeg');
        $response->assertSee('Dashboard');
        $response->assertSee('Rencana Kerja');
        $response->assertSee('Profil');
        $response->assertSee('Keluar');
    }

    public function test_login_form_keeps_route_contract_and_accessibility_markup(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('action="'.route('login.store').'"', false);
        $response->assertSee('name="username"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('LOGO-KSP.jpeg');
        $response->assertSee('csrf-token', false);
        $response->assertSee('x-bind:aria-label', false);
        $response->assertDontSee('Daftar');
        $response->assertDontSee('Lupa password');
    }

    public function test_profile_forms_keep_put_methods_and_loading_markup(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/profile');

        $response->assertOk();
        $response->assertSee('action="'.route('admin.profile.update').'"', false);
        $response->assertSee('action="'.route('admin.password.update').'"', false);
        $response->assertSee('name="_method" value="PUT"', false);
        $response->assertSee('x-bind:disabled', false);
        $response->assertSee('aria-busy', false);
    }
}
