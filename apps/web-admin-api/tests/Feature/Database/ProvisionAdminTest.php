<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProvisionAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_first_administrator_with_an_explicit_password(): void
    {
        $this->artisan('mms:provision-admin', [
            '--name' => 'Administrator MMS',
            '--username' => 'admin',
            '--email' => 'admin@example.test',
            '--password' => 'admin123',
        ])->assertSuccessful();

        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(Hash::check('admin123', $admin->password));
    }

    public function test_it_refuses_to_create_a_second_administrator(): void
    {
        User::factory()->admin()->create();

        $this->artisan('mms:provision-admin', [
            '--name' => 'Administrator Kedua',
            '--username' => 'admin-dua',
            '--email' => 'dua@example.test',
            '--password' => 'admin123',
        ])->assertFailed();
    }
}
