<?php

declare(strict_types=1);

namespace Tests\Feature;

use Livewire\Livewire;
use Tests\TestCase;

class BladeFoundationTest extends TestCase
{
    public function test_site_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login', absolute: false));
    }

    public function test_blade_layout_view_exists(): void
    {
        $this->assertTrue(view()->exists('layouts.base'));
        $this->assertTrue(view()->exists('layouts.app'));
        $this->assertTrue(view()->exists('layouts.admin'));
        $this->assertTrue(view()->exists('layouts.auth'));
    }

    public function test_livewire_package_is_available(): void
    {
        $this->assertTrue(class_exists(Livewire::class));
    }

    public function test_foundation_uses_non_database_runtime_drivers(): void
    {
        $this->assertNotSame('database', config('session.driver'));
        $this->assertNotSame('database', config('cache.default'));
        $this->assertSame('sync', config('queue.default'));
        $this->assertSame('public', config('filesystems.default'));
    }
}
