<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteContractTest extends TestCase
{
    public function test_expected_web_and_api_auth_routes_exist(): void
    {
        $expected = [
            'home' => ['GET', '/'],
            'login' => ['GET', 'login'],
            'login.store' => ['POST', 'login'],
            'logout' => ['POST', 'logout'],
            'admin.home' => ['GET', 'admin'],
            'admin.profile.edit' => ['GET', 'admin/profile'],
            'admin.profile.update' => ['PUT', 'admin/profile'],
            'admin.password.update' => ['PUT', 'admin/password'],
            'api.v1.health' => ['GET', 'api/v1/health'],
            'api.v1.auth.login' => ['POST', 'api/v1/auth/login'],
            'api.v1.auth.profile' => ['GET', 'api/v1/auth/profile'],
            'api.v1.auth.logout' => ['POST', 'api/v1/auth/logout'],
        ];

        foreach ($expected as $name => [$method, $uri]) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [$name] is missing.");
            $this->assertSame($uri, $route->uri());
            $this->assertContains($method, $route->methods());
        }
    }

    public function test_forbidden_auth_routes_do_not_exist(): void
    {
        $forbidden = [
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
            'verification.notice',
            'api.v1.auth.refresh',
            'api.v1.auth.logout-all',
        ];

        foreach ($forbidden as $name) {
            $this->assertNull(Route::getRoutes()->getByName($name), "Route [$name] must not exist.");
        }
    }
}
