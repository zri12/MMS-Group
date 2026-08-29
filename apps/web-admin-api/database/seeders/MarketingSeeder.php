<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class MarketingSeeder extends Seeder
{
    /**
     * @var list<array{code: string, name: string, area: string}>
     */
    private array $marketing = [
        ['code' => 'M01', 'name' => 'Deden', 'area' => 'Gedebage'],
        ['code' => 'M02', 'name' => 'Angil', 'area' => 'Rancasari'],
        ['code' => 'M03', 'name' => 'Ari', 'area' => 'Buahbatu'],
        ['code' => 'M04', 'name' => 'Feri', 'area' => 'Ujungberung'],
        ['code' => 'M05', 'name' => 'Sukma', 'area' => 'Cibiru'],
        ['code' => 'M06', 'name' => 'Sandi', 'area' => 'Antapani'],
        ['code' => 'M07', 'name' => 'Vikri', 'area' => 'Kiaracondong'],
        ['code' => 'M08', 'name' => 'Farhad', 'area' => 'Cicaheum'],
        ['code' => 'M09', 'name' => 'Doni', 'area' => 'Sukajadi'],
        ['code' => 'M10', 'name' => 'Faiz', 'area' => 'Lengkong'],
        ['code' => 'M11', 'name' => 'Agung', 'area' => 'Arcamanik'],
        ['code' => 'M12', 'name' => 'Faisal', 'area' => 'Cimahi'],
        ['code' => 'M13', 'name' => 'Agnes', 'area' => 'Cileunyi'],
    ];

    public function run(): void
    {
        $this->ensureSafeEnvironment();

        foreach ($this->marketing as $item) {
            $username = strtolower($item['code']).'.'.strtolower($item['name']);

            $user = User::query()->updateOrCreate(
                ['username' => $username],
                [
                    'name' => $item['name'],
                    'email' => strtolower($item['code']).'@mms.local',
                    'password' => Hash::make($this->seedPassword()),
                    'role' => UserRole::Marketing->value,
                    'is_active' => true,
                    'last_login_at' => null,
                ],
            );

            MarketingProfile::query()->updateOrCreate(
                ['code' => $item['code']],
                [
                    'user_id' => $user->id,
                    'phone' => null,
                    'area' => $item['area'],
                    'profile_photo_path' => null,
                ],
            );
        }
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
