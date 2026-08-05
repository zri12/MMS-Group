<?php

namespace Database\Factories;

use App\Models\MarketingProfile;
use App\Models\OperationalRecap;
use App\Models\OperationalRecapRow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OperationalRecapRow>
 */
class OperationalRecapRowFactory extends Factory
{
    protected $model = OperationalRecapRow::class;

    public function definition(): array
    {
        return [
            'operational_recap_id' => OperationalRecap::factory(),
            'marketing_profile_id' => MarketingProfile::factory(),
            'mg' => 'M01',
            'members_l' => 2,
            'members_m' => 1,
            'members_k' => 0,
            'members_s' => 0,
            'target_previous' => 6500000,
            'target_incoming' => 2000000,
            'target_outgoing' => 500000,
            'target_s' => 8000000,
            'drop_previous' => 3500000,
            'drop_current' => 2000000,
            'drop_total' => 5500000,
            'storting_previous' => 1000000,
            'storting_current' => 1300000,
            'storting_total' => 2300000,
            'percentage' => null,
            'previous_circulation' => 15000000,
            'current_circulation' => 18000000,
            'followed_by' => 'Admin KSP MMS',
            'morning_cash' => 3000000,
        ];
    }
}
