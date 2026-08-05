<?php

namespace Database\Seeders;

use App\Enums\DayName;
use App\Models\MarketingProfile;
use App\Models\OperationalRecap;
use App\Models\OperationalRecapRow;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class OperationalRecapSeeder extends Seeder
{
    /**
     * @var list<array{date: string, day: string, rows: array<string, array{target: int, drop: int, storting: int, previous_circulation: int, current_circulation: int}>}>
     */
    private array $fixtures = [
        [
            'date' => '2026-07-20',
            'day' => DayName::Monday->value,
            'rows' => [
                'M01' => ['target' => 8000000, 'drop' => 5500000, 'storting' => 2300000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M02' => ['target' => 8500000, 'drop' => 6000000, 'storting' => 2500000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M03' => ['target' => 9000000, 'drop' => 6500000, 'storting' => 2700000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M04' => ['target' => 9000000, 'drop' => 6500000, 'storting' => 2800000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M05' => ['target' => 9500000, 'drop' => 7000000, 'storting' => 2900000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M06' => ['target' => 9500000, 'drop' => 7000000, 'storting' => 3000000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M07' => ['target' => 10000000, 'drop' => 7500000, 'storting' => 3100000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M08' => ['target' => 10000000, 'drop' => 7500000, 'storting' => 3200000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M09' => ['target' => 10500000, 'drop' => 7500000, 'storting' => 3300000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M10' => ['target' => 10500000, 'drop' => 8000000, 'storting' => 3400000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M11' => ['target' => 11000000, 'drop' => 8000000, 'storting' => 3500000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M12' => ['target' => 11000000, 'drop' => 7500000, 'storting' => 2500000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M13' => ['target' => 7000000, 'drop' => 6500000, 'storting' => 2500000, 'previous_circulation' => 0, 'current_circulation' => 0],
            ],
        ],
        [
            'date' => '2026-07-21',
            'day' => DayName::Tuesday->value,
            'rows' => [
                'M01' => ['target' => 8100000, 'drop' => 5600000, 'storting' => 2310000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M02' => ['target' => 8600000, 'drop' => 6100000, 'storting' => 2510000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M03' => ['target' => 9100000, 'drop' => 6600000, 'storting' => 2710000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M04' => ['target' => 9200000, 'drop' => 6700000, 'storting' => 2810000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M05' => ['target' => 9600000, 'drop' => 7100000, 'storting' => 2910000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M06' => ['target' => 9700000, 'drop' => 7200000, 'storting' => 3010000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M07' => ['target' => 10100000, 'drop' => 7600000, 'storting' => 3110000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M08' => ['target' => 10200000, 'drop' => 7700000, 'storting' => 3210000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M09' => ['target' => 10600000, 'drop' => 7600000, 'storting' => 3310000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M10' => ['target' => 10700000, 'drop' => 8100000, 'storting' => 3410000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M11' => ['target' => 11100000, 'drop' => 8100000, 'storting' => 3510000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M12' => ['target' => 11200000, 'drop' => 7600000, 'storting' => 2510000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M13' => ['target' => 7100000, 'drop' => 6600000, 'storting' => 2510000, 'previous_circulation' => 0, 'current_circulation' => 0],
            ],
        ],
        [
            'date' => '2026-07-22',
            'day' => DayName::Wednesday->value,
            'rows' => [
                'M01' => ['target' => 8200000, 'drop' => 5700000, 'storting' => 2320000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M02' => ['target' => 8700000, 'drop' => 6200000, 'storting' => 2520000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M03' => ['target' => 9200000, 'drop' => 6700000, 'storting' => 2720000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M04' => ['target' => 9300000, 'drop' => 6800000, 'storting' => 2820000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M05' => ['target' => 9700000, 'drop' => 7200000, 'storting' => 2920000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M06' => ['target' => 9800000, 'drop' => 7300000, 'storting' => 3020000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M07' => ['target' => 10200000, 'drop' => 7700000, 'storting' => 3120000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M08' => ['target' => 10300000, 'drop' => 7800000, 'storting' => 3220000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M09' => ['target' => 10700000, 'drop' => 7700000, 'storting' => 3320000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M10' => ['target' => 10800000, 'drop' => 8200000, 'storting' => 3420000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M11' => ['target' => 11200000, 'drop' => 8200000, 'storting' => 3520000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M12' => ['target' => 11300000, 'drop' => 7700000, 'storting' => 2520000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M13' => ['target' => 7200000, 'drop' => 6700000, 'storting' => 2520000, 'previous_circulation' => 0, 'current_circulation' => 0],
            ],
        ],
        [
            'date' => '2026-07-23',
            'day' => DayName::Thursday->value,
            'rows' => [
                'M01' => ['target' => 8300000, 'drop' => 5800000, 'storting' => 2330000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M02' => ['target' => 8800000, 'drop' => 6300000, 'storting' => 2530000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M03' => ['target' => 9300000, 'drop' => 6800000, 'storting' => 2730000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M04' => ['target' => 9400000, 'drop' => 6900000, 'storting' => 2830000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M05' => ['target' => 9800000, 'drop' => 7300000, 'storting' => 2930000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M06' => ['target' => 9900000, 'drop' => 7400000, 'storting' => 3030000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M07' => ['target' => 10300000, 'drop' => 7800000, 'storting' => 3130000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M08' => ['target' => 10400000, 'drop' => 7900000, 'storting' => 3230000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M09' => ['target' => 10800000, 'drop' => 7800000, 'storting' => 3330000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M10' => ['target' => 10900000, 'drop' => 8300000, 'storting' => 3430000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M11' => ['target' => 11300000, 'drop' => 8300000, 'storting' => 3530000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M12' => ['target' => 11400000, 'drop' => 7800000, 'storting' => 2530000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M13' => ['target' => 7300000, 'drop' => 6800000, 'storting' => 2530000, 'previous_circulation' => 0, 'current_circulation' => 0],
            ],
        ],
        [
            'date' => '2026-07-24',
            'day' => DayName::Friday->value,
            'rows' => [
                'M01' => ['target' => 8400000, 'drop' => 5900000, 'storting' => 2340000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M02' => ['target' => 8900000, 'drop' => 6400000, 'storting' => 2540000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M03' => ['target' => 9400000, 'drop' => 6900000, 'storting' => 2740000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M04' => ['target' => 9500000, 'drop' => 7000000, 'storting' => 2840000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M05' => ['target' => 9900000, 'drop' => 7400000, 'storting' => 2940000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M06' => ['target' => 10000000, 'drop' => 7500000, 'storting' => 3040000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M07' => ['target' => 10400000, 'drop' => 7900000, 'storting' => 3140000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M08' => ['target' => 10500000, 'drop' => 8000000, 'storting' => 3240000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M09' => ['target' => 10900000, 'drop' => 7900000, 'storting' => 3340000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M10' => ['target' => 11000000, 'drop' => 8400000, 'storting' => 3440000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M11' => ['target' => 11400000, 'drop' => 8400000, 'storting' => 3540000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M12' => ['target' => 11500000, 'drop' => 7900000, 'storting' => 2540000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M13' => ['target' => 7400000, 'drop' => 6900000, 'storting' => 2540000, 'previous_circulation' => 0, 'current_circulation' => 0],
            ],
        ],
        [
            'date' => '2026-07-25',
            'day' => DayName::Saturday->value,
            'rows' => [
                'M01' => ['target' => 8500000, 'drop' => 6000000, 'storting' => 2350000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M02' => ['target' => 9000000, 'drop' => 6500000, 'storting' => 2550000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M03' => ['target' => 9500000, 'drop' => 7000000, 'storting' => 2750000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M04' => ['target' => 9600000, 'drop' => 7100000, 'storting' => 2850000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M05' => ['target' => 10000000, 'drop' => 7500000, 'storting' => 2950000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M06' => ['target' => 10100000, 'drop' => 7600000, 'storting' => 3050000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M07' => ['target' => 10500000, 'drop' => 8000000, 'storting' => 3150000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M08' => ['target' => 10600000, 'drop' => 8100000, 'storting' => 3250000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M09' => ['target' => 11000000, 'drop' => 8000000, 'storting' => 3350000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M10' => ['target' => 11100000, 'drop' => 8500000, 'storting' => 3450000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M11' => ['target' => 11500000, 'drop' => 8500000, 'storting' => 3550000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M12' => ['target' => 11600000, 'drop' => 8000000, 'storting' => 2550000, 'previous_circulation' => 0, 'current_circulation' => 0],
                'M13' => ['target' => 7500000, 'drop' => 7000000, 'storting' => 2550000, 'previous_circulation' => 0, 'current_circulation' => 0],
            ],
        ],
    ];

    public function run(): void
    {
        $this->ensureSafeEnvironment();

        $admin = User::query()->where('username', config('mms.seed.admin_username'))->firstOrFail();
        $profiles = MarketingProfile::query()->orderBy('code')->get()->keyBy('code');

        foreach ($this->fixtures as $fixture) {
            $recap = OperationalRecap::query()->updateOrCreate(
                ['report_number' => 'RKP-'.str_replace('-', '', $fixture['date'])],
                [
                    'recap_date' => $fixture['date'],
                    'day_name' => $fixture['day'],
                    'status' => 'Draft',
                    'created_by' => $admin->id,
                ],
            );

            foreach ($fixture['rows'] as $code => $amounts) {
                $profile = $profiles->get($code);

                if (! $profile instanceof MarketingProfile) {
                    continue;
                }

                // Circulation values are explicit development fixtures, not business formulas.
                OperationalRecapRow::query()->updateOrCreate(
                    [
                        'operational_recap_id' => $recap->id,
                        'marketing_profile_id' => $profile->id,
                    ],
                    [
                        'mg' => $code,
                        'members_l' => 1,
                        'members_m' => 1,
                        'members_k' => 1,
                        'members_s' => 0,
                        'target_previous' => $amounts['target'],
                        'target_incoming' => 0,
                        'target_outgoing' => 0,
                        'target_s' => $amounts['target'],
                        'drop_previous' => 0,
                        'drop_current' => $amounts['drop'],
                        'drop_total' => $amounts['drop'],
                        'storting_previous' => 0,
                        'storting_current' => $amounts['storting'],
                        'storting_total' => $amounts['storting'],
                        'percentage' => null,
                        'previous_circulation' => $amounts['previous_circulation'],
                        'current_circulation' => $amounts['current_circulation'],
                        'followed_by' => 'Admin KSP MMS',
                        'morning_cash' => 2500000,
                    ],
                );
            }
        }
    }

    private function ensureSafeEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Development seeder tidak boleh dijalankan pada production.');
        }
    }
}
