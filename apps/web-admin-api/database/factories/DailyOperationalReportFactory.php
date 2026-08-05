<?php

namespace Database\Factories;

use App\Enums\DayName;
use App\Enums\SyncStatus;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DailyOperationalReport>
 */
class DailyOperationalReportFactory extends Factory
{
    protected $model = DailyOperationalReport::class;

    public function definition(): array
    {
        return [
            'local_uuid' => (string) Str::uuid(),
            'marketing_profile_id' => MarketingProfile::factory(),
            'report_date' => '2026-07-20',
            'report_time' => '16:00:00',
            'day_name' => DayName::Monday->value,
            'resort' => 'Gedebage',
            'storting' => 2300000,
            'insurance_amount' => 150000,
            'drop_amount' => 5500000,
            'withdrawal_saving' => 200000,
            'previous_target_amount' => 6500000,
            'previous_target_people' => 3,
            'incoming_target_amount' => 2000000,
            'incoming_target_people' => 2,
            'outgoing_target_amount' => 500000,
            'outgoing_target_people' => 1,
            'total_target_amount' => 8000000,
            'total_target_people' => 4,
            'new_drop' => 2000000,
            'continued_drop' => 3500000,
            'notes' => 'Operasional berjalan normal.',
            'sync_status' => SyncStatus::Synced->value,
        ];
    }
}
