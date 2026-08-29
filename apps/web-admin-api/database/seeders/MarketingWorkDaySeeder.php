<?php

namespace Database\Seeders;

use App\Enums\DayName;
use App\Models\MarketingProfile;
use App\Models\MarketingWorkDay;
use Illuminate\Database\Seeder;
use RuntimeException;

class MarketingWorkDaySeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSafeEnvironment();

        MarketingProfile::query()->each(function (MarketingProfile $profile): void {
            foreach (DayName::values() as $dayName) {
                MarketingWorkDay::query()->updateOrCreate(
                    [
                        'marketing_profile_id' => $profile->id,
                        'day_name' => $dayName,
                    ],
                    [],
                );
            }
        });
    }

    private function ensureSafeEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Development seeder tidak boleh dijalankan pada production.');
        }
    }
}
