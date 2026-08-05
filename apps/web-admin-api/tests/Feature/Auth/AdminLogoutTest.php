<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_logout_with_post_request(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/logout');

        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('flash_message', 'Anda telah keluar.');
        $this->assertGuest();
    }

    public function test_guest_post_logout_redirects_to_login(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect(route('login', absolute: false));
        $this->assertGuest();
    }

    public function test_logged_out_session_cannot_access_admin_route(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/logout')->assertRedirect(route('login', absolute: false));

        $this->get('/admin')->assertRedirect(route('login', absolute: false));
    }

    public function test_logout_is_not_available_as_get_request(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/logout');

        $response->assertStatus(405);
        $this->assertAuthenticatedAs($admin);
    }
}
