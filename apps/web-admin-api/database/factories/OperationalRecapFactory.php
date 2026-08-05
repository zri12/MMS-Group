<?php

namespace Database\Factories;

use App\Enums\DayName;
use App\Models\OperationalRecap;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OperationalRecap>
 */
class OperationalRecapFactory extends Factory
{
    protected $model = OperationalRecap::class;

    public function definition(): array
    {
        do {
            $dateTime = fake()->unique()->dateTimeBetween('2026-07-20', '2026-12-31');
            $dayOfWeek = (int) $dateTime->format('N');
        } while ($dayOfWeek === 7);

        $date = $dateTime->format('Y-m-d');
        $dayName = match ($dayOfWeek) {
            1 => DayName::Monday,
            2 => DayName::Tuesday,
            3 => DayName::Wednesday,
            4 => DayName::Thursday,
            5 => DayName::Friday,
            6 => DayName::Saturday,
        };

        return [
            'report_number' => 'RKP-'.str_replace('-', '', $date),
            'recap_date' => $date,
            'day_name' => $dayName->value,
            'status' => 'Draft',
            'created_by' => User::factory()->admin(),
        ];
    }
}
