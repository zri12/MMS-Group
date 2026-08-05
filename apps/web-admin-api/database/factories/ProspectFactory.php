<?php

namespace Database\Factories;

use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Prospect>
 */
class ProspectFactory extends Factory
{
    protected $model = Prospect::class;

    public function definition(): array
    {
        return [
            'local_uuid' => (string) Str::uuid(),
            'marketing_profile_id' => MarketingProfile::factory(),
            'name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->streetAddress(),
            'business' => fake()->randomElement(['Toko Kelontong', 'Warung Sembako', 'Grosir']),
            'status' => ProspectStatus::New->value,
            'initial_visit_result' => 'Bersedia menerima penjelasan produk.',
            'notes' => null,
            'resort' => fake()->randomElement(['Gedebage', 'Rancasari', 'Buahbatu']),
            'input_date' => '2026-07-20',
            'input_time' => '08:30:00',
            'latitude' => -6.9388000,
            'longitude' => 107.7079000,
            'location_address' => 'Gedebage, Kota Bandung',
            'sync_status' => SyncStatus::Synced->value,
        ];
    }

    public function newStatus(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ProspectStatus::New->value]);
    }

    public function interested(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ProspectStatus::Interested->value]);
    }

    public function followUp(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ProspectStatus::FollowUp->value]);
    }

    public function notInterested(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ProspectStatus::NotInterested->value]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ProspectStatus::Completed->value]);
    }

    public function synced(): static
    {
        return $this->state(fn (array $attributes) => ['sync_status' => SyncStatus::Synced->value]);
    }

    public function pendingSync(): static
    {
        return $this->state(fn (array $attributes) => ['sync_status' => SyncStatus::Pending->value]);
    }
}
