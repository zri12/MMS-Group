<?php

namespace Database\Seeders;

use App\Enums\DayName;
use App\Enums\ScheduleStatus;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class MarketingScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSafeEnvironment();

        $m01 = MarketingProfile::query()->where('code', 'M01')->firstOrFail();
        $admin = User::query()->where('username', config('mms.seed.admin_username'))->firstOrFail();

        foreach ($this->schedules() as $data) {
            $prospect = Prospect::query()->where('name', $data['prospect_name'])->first();

            $schedule = MarketingSchedule::query()
                ->where('marketing_profile_id', $m01->id)
                ->where('agenda', $data['agenda'])
                ->where('consumer_name_snapshot', $data['consumer_name_snapshot'])
                ->firstOrNew([
                    'marketing_profile_id' => $m01->id,
                    'agenda' => $data['agenda'],
                    'consumer_name_snapshot' => $data['consumer_name_snapshot'],
                ]);

            $schedule->fill([
                'prospect_id' => $prospect?->id,
                'day_name' => $data['day_name'],
                'schedule_date' => $data['schedule_date'],
                'start_time' => $data['start_time'],
                'end_time' => null,
                'area' => $data['area'],
                'resort' => $data['area'],
                'destination' => $data['area'].', Kota Bandung',
                'note' => null,
                'status' => $data['status'],
                'created_by' => $admin->id,
            ])->save();
        }
    }

    /**
     * @return list<array<string, string>>
     */
    private function schedules(): array
    {
        return [
            [
                'prospect_name' => 'Ahmad Hidayat',
                'consumer_name_snapshot' => 'Ahmad Hidayat',
                'day_name' => DayName::Monday->value,
                'schedule_date' => '2026-07-20',
                'start_time' => '08:30:00',
                'agenda' => 'Presentasi produk tabungan',
                'area' => 'Gedebage',
                'status' => ScheduleStatus::Completed->value,
            ],
            [
                'prospect_name' => '',
                'consumer_name_snapshot' => 'Herman Malik',
                'day_name' => DayName::Monday->value,
                'schedule_date' => '2026-07-20',
                'start_time' => '10:00:00',
                'agenda' => 'Survei pengajuan anggota',
                'area' => 'Buahbatu',
                'status' => ScheduleStatus::InProgress->value,
            ],
            [
                'prospect_name' => 'Siti Nurjanah',
                'consumer_name_snapshot' => 'Siti Nurjanah',
                'day_name' => DayName::Monday->value,
                'schedule_date' => '2026-07-20',
                'start_time' => '13:30:00',
                'agenda' => 'Follow up produk simpanan',
                'area' => 'Rancasari',
                'status' => ScheduleStatus::NotVisited->value,
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
