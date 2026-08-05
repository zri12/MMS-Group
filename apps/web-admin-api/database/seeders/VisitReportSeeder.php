<?php

namespace Database\Seeders;

use App\Enums\DayName;
use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use App\Enums\VisitResult;
use App\Models\Prospect;
use App\Models\VisitReport;
use Illuminate\Database\Seeder;
use RuntimeException;

class VisitReportSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSafeEnvironment();

        $ahmad = Prospect::query()->where('name', 'Ahmad Hidayat')->firstOrFail();

        VisitReport::query()->updateOrCreate(
            ['local_uuid' => '00000000-0000-4000-8000-000000000401'],
            [
                'prospect_id' => $ahmad->id,
                'marketing_profile_id' => $ahmad->marketing_profile_id,
                'visit_date' => '2026-07-20',
                'visit_time' => '09:30:00',
                'day_name' => DayName::Monday->value,
                'visit_purpose' => 'Presentasi produk tabungan',
                'visit_result' => VisitResult::Met->value,
                'prospect_status' => ProspectStatus::Interested->value,
                'notes' => 'Prospek bersedia menerima follow up.',
                'follow_up_date' => '2026-07-22',
                'photo_path' => 'visit-reports/development/visit-reference.webp',
                'photo_caption' => 'Foto kunjungan development',
                'resort' => 'Gedebage',
                'latitude' => -6.9388000,
                'longitude' => 107.7079000,
                'location_address' => 'Gedebage, Kota Bandung',
                'sync_status' => SyncStatus::Synced->value,
            ],
        );
    }

    private function ensureSafeEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Development seeder tidak boleh dijalankan pada production.');
        }
    }
}
