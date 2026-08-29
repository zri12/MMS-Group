<?php

namespace Database\Seeders;

use App\Enums\DayName;
use App\Enums\TrackingPointType;
use App\Enums\TrackingStatus;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use Illuminate\Database\Seeder;
use RuntimeException;

class TrackingSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureSafeEnvironment();

        $m01 = MarketingProfile::query()->where('code', 'M01')->firstOrFail();
        $schedule = MarketingSchedule::query()
            ->where('marketing_profile_id', $m01->id)
            ->whereDate('schedule_date', '2026-07-20')
            ->orderBy('start_time')
            ->first();

        $session = TrackingSession::query()->updateOrCreate(
            ['local_uuid' => '00000000-0000-4000-8000-000000000501'],
            [
                'marketing_profile_id' => $m01->id,
                'schedule_id' => $schedule?->id,
                'session_date' => '2026-07-20',
                'day_name' => DayName::Monday->value,
                'started_at' => '2026-07-20 08:05:00',
                'ended_at' => '2026-07-20 15:30:00',
                'status' => TrackingStatus::Offline->value,
                'distance_meters' => 8400,
                'visit_count' => 3,
            ],
        );

        foreach ($this->points($session->id) as $point) {
            TrackingPoint::query()->updateOrCreate(
                ['local_uuid' => $point['local_uuid']],
                $point,
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function points(int $sessionId): array
    {
        return [
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000511',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9401000,
                'longitude' => 107.7080000,
                'accuracy_meters' => 8.50,
                'speed_mps' => 0.00,
                'heading' => 0.00,
                'altitude_meters' => 710.00,
                'address' => 'Kantor KSP MMS, Bandung',
                'point_type' => TrackingPointType::Start->value,
                'recorded_at' => '2026-07-20 08:05:00',
                'received_at' => '2026-07-20 08:05:05',
                'created_at' => '2026-07-20 08:05:05',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000512',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9388000,
                'longitude' => 107.7079000,
                'accuracy_meters' => 9.00,
                'speed_mps' => 1.20,
                'heading' => 120.00,
                'altitude_meters' => 711.00,
                'address' => 'Gedebage, Kota Bandung',
                'point_type' => TrackingPointType::Journey->value,
                'recorded_at' => '2026-07-20 08:30:00',
                'received_at' => '2026-07-20 08:30:05',
                'created_at' => '2026-07-20 08:30:05',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000513',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9388000,
                'longitude' => 107.7079000,
                'accuracy_meters' => 7.00,
                'speed_mps' => 0.00,
                'heading' => 0.00,
                'altitude_meters' => 711.00,
                'address' => 'Ahmad Hidayat, Gedebage',
                'point_type' => TrackingPointType::Visit->value,
                'recorded_at' => '2026-07-20 09:30:00',
                'received_at' => '2026-07-20 09:30:05',
                'created_at' => '2026-07-20 09:30:05',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000514',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9378000,
                'longitude' => 107.7090000,
                'accuracy_meters' => 8.00,
                'speed_mps' => 1.50,
                'heading' => 80.00,
                'altitude_meters' => 709.00,
                'address' => 'Gedebage Timur, Kota Bandung',
                'point_type' => TrackingPointType::Journey->value,
                'recorded_at' => '2026-07-20 09:50:00',
                'received_at' => '2026-07-20 09:50:05',
                'created_at' => '2026-07-20 09:50:05',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000515',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9378000,
                'longitude' => 107.7090000,
                'accuracy_meters' => 7.00,
                'speed_mps' => 0.00,
                'heading' => 0.00,
                'altitude_meters' => 709.00,
                'address' => 'Herman Malik, Gedebage Timur',
                'point_type' => TrackingPointType::Visit->value,
                'recorded_at' => '2026-07-20 10:00:00',
                'received_at' => '2026-07-20 10:00:05',
                'created_at' => '2026-07-20 10:00:05',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000516',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9541000,
                'longitude' => 107.6817000,
                'accuracy_meters' => 10.00,
                'speed_mps' => 1.50,
                'heading' => 210.00,
                'altitude_meters' => 705.00,
                'address' => 'Rancasari, Kota Bandung',
                'point_type' => TrackingPointType::Journey->value,
                'recorded_at' => '2026-07-20 13:20:00',
                'received_at' => '2026-07-20 13:20:05',
                'created_at' => '2026-07-20 13:20:05',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000517',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9541000,
                'longitude' => 107.6817000,
                'accuracy_meters' => 8.00,
                'speed_mps' => 0.00,
                'heading' => 0.00,
                'altitude_meters' => 705.00,
                'address' => 'Siti Nurjanah, Rancasari',
                'point_type' => TrackingPointType::Visit->value,
                'recorded_at' => '2026-07-20 13:30:00',
                'received_at' => '2026-07-20 13:30:05',
                'created_at' => '2026-07-20 13:30:05',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000518',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9389000,
                'longitude' => 107.7069000,
                'accuracy_meters' => 9.00,
                'speed_mps' => 1.40,
                'heading' => 20.00,
                'altitude_meters' => 710.00,
                'address' => 'Perjalanan kembali ke Gedebage',
                'point_type' => TrackingPointType::Journey->value,
                'recorded_at' => '2026-07-20 15:00:00',
                'received_at' => '2026-07-20 15:00:05',
                'created_at' => '2026-07-20 15:00:05',
            ],
            [
                'local_uuid' => '00000000-0000-4000-8000-000000000519',
                'tracking_session_id' => $sessionId,
                'latitude' => -6.9389000,
                'longitude' => 107.7069000,
                'accuracy_meters' => 8.00,
                'speed_mps' => 0.00,
                'heading' => 0.00,
                'altitude_meters' => 710.00,
                'address' => 'Gedebage, Kota Bandung',
                'point_type' => TrackingPointType::Finish->value,
                'recorded_at' => '2026-07-20 15:30:00',
                'received_at' => '2026-07-20 15:30:05',
                'created_at' => '2026-07-20 15:30:05',
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
