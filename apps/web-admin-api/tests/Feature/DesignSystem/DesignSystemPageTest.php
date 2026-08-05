<?php

declare(strict_types=1);

namespace Tests\Feature\DesignSystem;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DesignSystemPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_design_system_route_is_available_in_testing(): void
    {
        $route = Route::getRoutes()->getByName('admin.design-system');

        $this->assertNotNull($route);
        $this->assertSame('admin/design-system', $route->uri());
        $this->assertContains('GET', $route->methods());
    }

    public function test_design_system_route_is_guarded_from_production_registration(): void
    {
        $routes = File::get(base_path('routes/admin.php'));

        $this->assertStringContainsString("app()->environment(['local', 'testing'])", $routes);
        $this->assertStringContainsString("Route::view('design-system'", $routes);
    }

    public function test_guest_is_redirected_from_design_system_page(): void
    {
        $this->get('/admin/design-system')
            ->assertRedirect(route('login', absolute: false));
    }

    public function test_marketing_user_receives_forbidden_from_design_system_page(): void
    {
        $marketing = User::factory()->marketing()->create();

        $this->actingAs($marketing)->get('/admin/design-system')
            ->assertForbidden()
            ->assertSee('Akses tidak tersedia.');
    }

    public function test_active_admin_can_open_design_system_catalog(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/design-system');

        $response->assertOk();
        $response->assertSee('MMS Blade Design System');
        $response->assertSee('Buttons');
        $response->assertSee('Form');
        $response->assertSee('Feedback dan Status');
        $response->assertSee('Table');
        $response->assertDontSee('APP_KEY');
        $response->assertDontSee('DB_PASSWORD');
    }
}
