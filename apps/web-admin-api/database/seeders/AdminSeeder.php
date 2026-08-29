<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSafeEnvironment();

        User::query()->updateOrCreate(
            ['username' => config('mms.seed.admin_username')],
            [
                'name' => 'Administrator MMS',
                'email' => config('mms.seed.admin_email'),
                'password' => Hash::make($this->seedPassword()),
                'role' => UserRole::Admin->value,
                'is_active' => true,
                'last_login_at' => null,
            ],
        );
    }

    private function ensureSafeEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Development seeder tidak boleh dijalankan pada production.');
        }
    }

    private function seedPassword(): string
    {
        $password = config('mms.seed.default_password');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('MMS_SEED_DEFAULT_PASSWORD wajib diisi untuk menjalankan development seeder.');
        }

        return $password;
    }
}
