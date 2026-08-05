<?php

namespace Database\Factories;

use App\Enums\TrackingPointType;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TrackingPoint>
 */
class TrackingPointFactory extends Factory
{
    protected $model = TrackingPoint::class;

    public function definition(): array
    {
        return [
            'local_uuid' => (string) Str::uuid(),
            'tracking_session_id' => TrackingSession::factory(),
            'latitude' => -6.9388000,
            'longitude' => 107.7079000,
            'accuracy_meters' => 8.50,
            'speed_mps' => 1.20,
            'heading' => 120.00,
            'altitude_meters' => 710.00,
            'address' => 'Gedebage, Kota Bandung',
            'point_type' => TrackingPointType::Journey->value,
            'recorded_at' => '2026-07-20 08:06:00',
            'received_at' => '2026-07-20 08:06:05',
            'created_at' => now(),
        ];
    }
}
