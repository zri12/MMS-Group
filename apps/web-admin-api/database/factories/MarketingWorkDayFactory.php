<?php

namespace Database\Factories;

use App\Enums\DayName;
use App\Models\MarketingProfile;
use App\Models\MarketingWorkDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketingWorkDay>
 */
class MarketingWorkDayFactory extends Factory
{
    protected $model = MarketingWorkDay::class;

    public function definition(): array
    {
        return [
            'marketing_profile_id' => MarketingProfile::factory(),
            'day_name' => fake()->randomElement(DayName::values()),
        ];
    }
}
