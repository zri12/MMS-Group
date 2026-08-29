<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed deterministic MMS development data.
     *
     * This seeder is restricted to local and testing environments.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Development seeder hanya boleh dijalankan pada local atau testing.');
        }

        $this->call([
            AdminSeeder::class,
            MarketingSeeder::class,
            MarketingWorkDaySeeder::class,
            ProspectSeeder::class,
            MemberSeeder::class,
            MarketingScheduleSeeder::class,
            DailyOperationalReportSeeder::class,
            VisitReportSeeder::class,
            TrackingSeeder::class,
            OperationalRecapSeeder::class,
        ]);
    }
}
