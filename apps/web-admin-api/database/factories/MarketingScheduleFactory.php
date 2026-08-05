<?php

namespace Database\Factories;

use App\Enums\DayName;
use App\Enums\ScheduleStatus;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketingSchedule>
 */
class MarketingScheduleFactory extends Factory
{
    protected $model = MarketingSchedule::class;

    public function definition(): array
    {
        return [
            'marketing_profile_id' => MarketingProfile::factory(),
            'prospect_id' => fn (array $attributes) => Prospect::factory()->create([
                'marketing_profile_id' => $attributes['marketing_profile_id'],
            ])->id,
            'day_name' => DayName::Monday->value,
            'schedule_date' => '2026-07-20',
            'start_time' => '08:30:00',
            'end_time' => null,
            'consumer_name_snapshot' => 'Ahmad Hidayat',
            'agenda' => 'Presentasi produk tabungan',
            'area' => 'Gedebage',
            'resort' => 'Gedebage',
            'destination' => 'Gedebage, Kota Bandung',
            'note' => null,
            'status' => ScheduleStatus::NotVisited->value,
            'created_by' => User::factory()->admin(),
        ];
    }

    public function forProspect(Prospect $prospect): static
    {
        return $this->state(fn (array $attributes) => [
            'marketing_profile_id' => $prospect->marketing_profile_id,
            'prospect_id' => $prospect->id,
            'consumer_name_snapshot' => $prospect->name,
            'area' => $prospect->resort,
            'resort' => $prospect->resort,
            'destination' => $prospect->location_address,
        ]);
    }

    public function notVisited(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ScheduleStatus::NotVisited->value]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ScheduleStatus::InProgress->value]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ScheduleStatus::Completed->value]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ScheduleStatus::Canceled->value]);
    }
}
