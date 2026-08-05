<?php

namespace Database\Factories;

use App\Enums\DayName;
use App\Enums\TrackingStatus;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\TrackingSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TrackingSession>
 */
class TrackingSessionFactory extends Factory
{
    protected $model = TrackingSession::class;

    public function definition(): array
    {
        return [
            'local_uuid' => (string) Str::uuid(),
            'marketing_profile_id' => MarketingProfile::factory(),
            'schedule_id' => fn (array $attributes) => MarketingSchedule::factory()->create([
                'marketing_profile_id' => $attributes['marketing_profile_id'],
            ])->id,
            'session_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
            'started_at' => '2026-07-20 08:05:00',
            'ended_at' => '2026-07-20 15:30:00',
            'status' => TrackingStatus::Offline->value,
            'distance_meters' => 8400,
            'visit_count' => 3,
        ];
    }

    public function forSchedule(MarketingSchedule $schedule): static
    {
        return $this->state(fn (array $attributes) => [
            'marketing_profile_id' => $schedule->marketing_profile_id,
            'schedule_id' => $schedule->id,
            'session_date' => $schedule->schedule_date,
            'day_name' => $schedule->day_name,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TrackingStatus::Active->value,
            'ended_at' => null,
        ]);
    }

    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TrackingStatus::Offline->value,
            'ended_at' => '2026-07-20 15:30:00',
        ]);
    }
}
