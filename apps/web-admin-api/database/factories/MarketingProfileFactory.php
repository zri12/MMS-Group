<?php

namespace Database\Factories;

use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketingProfile>
 */
class MarketingProfileFactory extends Factory
{
    protected $model = MarketingProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->marketing(),
            'code' => 'M'.fake()->unique()->numberBetween(14, 99),
            'phone' => fake()->optional()->numerify('08##########'),
            'area' => fake()->randomElement(['Gedebage', 'Rancasari', 'Buahbatu', 'Cibiru']),
            'profile_photo_path' => null,
        ];
    }
}
