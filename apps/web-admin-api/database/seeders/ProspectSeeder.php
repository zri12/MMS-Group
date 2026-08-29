<?php

namespace Database\Seeders;

use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProspectSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSafeEnvironment();

        $m01 = MarketingProfile::query()->where('code', 'M01')->firstOrFail();

        foreach ($this->prospects() as $data) {
            Prospect::query()->updateOrCreate(
                ['local_uuid' => $data['local_uuid']],
                $data + [
                    'marketing_profile_id' => $m01->id,
                    'sync_status' => SyncStatus::Synced->value,
                ],
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function prospects(): array
    {
        return [
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000101',
                'name' => 'Ahmad Hidayat',
                'phone' => '081211112201',
                'address' => 'Jl. Gedebage Selatan No. 21',
                'business' => 'Toko Kelontong',
                'status' => ProspectStatus::Interested->value,
                'initial_visit_result' => 'Bersedia menerima presentasi produk',
                'notes' => 'Tertarik produk tabungan usaha.',
                'resort' => 'Gedebage',
                'input_date' => '2026-07-20',
                'input_time' => '08:30:00',
                'latitude' => -6.9388000,
                'longitude' => 107.7079000,
                'location_address' => 'Gedebage, Kota Bandung',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000102',
                'name' => 'Siti Nurjanah',
                'phone' => '081211112202',
                'address' => 'Jl. Rancasari No. 8',
                'business' => 'Warung Sembako',
                'status' => ProspectStatus::FollowUp->value,
                'initial_visit_result' => 'Meminta follow up produk simpanan',
                'notes' => 'Perlu dikunjungi ulang.',
                'resort' => 'Rancasari',
                'input_date' => '2026-07-20',
                'input_time' => '10:15:00',
                'latitude' => -6.9541000,
                'longitude' => 107.6817000,
                'location_address' => 'Rancasari, Kota Bandung',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000103',
                'name' => 'Toko Berkah Jaya',
                'phone' => '081211112203',
                'address' => 'Jl. Buahbatu No. 14',
                'business' => 'Grosir',
                'status' => ProspectStatus::New->value,
                'initial_visit_result' => 'Baru dicatat untuk kunjungan awal',
                'notes' => null,
                'resort' => 'Buahbatu',
                'input_date' => '2026-07-20',
                'input_time' => '13:00:00',
                'latitude' => -6.9469000,
                'longitude' => 107.6388000,
                'location_address' => 'Buahbatu, Kota Bandung',
            ],
        ];
    }

    private function ensureSafeEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Development seeder tidak boleh dijalankan pada production.');
        }
    }
}
