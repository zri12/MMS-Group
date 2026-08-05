<?php

namespace Database\Seeders;

use App\Enums\DayName;
use App\Enums\SyncStatus;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use Illuminate\Database\Seeder;
use RuntimeException;

class DailyOperationalReportSeeder extends Seeder
{
    /**
     * @var array<string, array{target: int, drop: int, storting: int}>
     */
    private array $mondayAggregates = [
        'M01' => ['target' => 8000000, 'drop' => 5500000, 'storting' => 2300000],
        'M02' => ['target' => 8500000, 'drop' => 6000000, 'storting' => 2500000],
        'M03' => ['target' => 9000000, 'drop' => 6500000, 'storting' => 2700000],
        'M04' => ['target' => 9000000, 'drop' => 6500000, 'storting' => 2800000],
        'M05' => ['target' => 9500000, 'drop' => 7000000, 'storting' => 2900000],
        'M06' => ['target' => 9500000, 'drop' => 7000000, 'storting' => 3000000],
        'M07' => ['target' => 10000000, 'drop' => 7500000, 'storting' => 3100000],
        'M08' => ['target' => 10000000, 'drop' => 7500000, 'storting' => 3200000],
        'M09' => ['target' => 10500000, 'drop' => 7500000, 'storting' => 3300000],
        'M10' => ['target' => 10500000, 'drop' => 8000000, 'storting' => 3400000],
        'M11' => ['target' => 11000000, 'drop' => 8000000, 'storting' => 3500000],
        'M12' => ['target' => 11000000, 'drop' => 7500000, 'storting' => 2500000],
        'M13' => ['target' => 7000000, 'drop' => 6500000, 'storting' => 2500000],
    ];

    public function run(): void
    {
        $this->ensureSafeEnvironment();

        MarketingProfile::query()
            ->orderBy('code')
            ->get()
            ->each(function (MarketingProfile $profile, int $index): void {
                $amounts = $this->mondayAggregates[$profile->code];

                DailyOperationalReport::query()->updateOrCreate(
                    ['local_uuid' => sprintf('00000000-0000-4000-8000-0000000003%02d', $index + 1)],
                    [
                        'marketing_profile_id' => $profile->id,
                        'report_date' => '2026-07-20',
                        'report_time' => '16:00:00',
                        'day_name' => DayName::Monday->value,
                        'resort' => $profile->area,
                        'storting' => $amounts['storting'],
                        'insurance_amount' => 150000 + ($index * 10000),
                        'drop_amount' => $amounts['drop'],
                        'withdrawal_saving' => 200000 + ($index * 10000),
                        'previous_target_amount' => max(0, $amounts['target'] - 1500000),
                        'previous_target_people' => 2 + ($index % 3),
                        'incoming_target_amount' => 2000000,
                        'incoming_target_people' => 2,
                        'outgoing_target_amount' => 500000,
                        'outgoing_target_people' => 1,
                        'total_target_amount' => $amounts['target'],
                        'total_target_people' => 4 + ($index % 4),
                        'new_drop' => min(2500000, $amounts['drop']),
                        'continued_drop' => max(0, $amounts['drop'] - 2500000),
                        'notes' => 'Laporan operasional development '.$profile->code.'.',
                        'sync_status' => SyncStatus::Synced->value,
                    ],
                );
            });
    }

    private function ensureSafeEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Development seeder tidak boleh dijalankan pada production.');
        }
    }
}
