<?php

namespace Database\Factories;

use App\Enums\MemberApprovalStatus;
use App\Enums\SyncStatus;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'local_uuid' => (string) Str::uuid(),
            'marketing_profile_id' => MarketingProfile::factory(),
            'source_prospect_id' => null,
            'resort' => 'Gedebage',
            'input_date' => '2026-07-20',
            'input_time' => '09:00:00',
            'name' => fake()->name(),
            'member_number' => fake()->unique()->numerify('DEV-#####'),
            'loan_number' => fake()->unique()->numerify('LN-#####'),
            'address' => fake()->streetAddress(),
            'phone' => fake()->numerify('08##########'),
            'business' => fake()->randomElement(['Toko Kelontong', 'Warung Sembako', 'Grosir']),
            'loan_amount' => 5000000,
            'installment_amount' => 250000,
            'insurance_amount' => 100000,
            'collateral' => 'BPKB motor',
            'approval_status' => MemberApprovalStatus::Pending->value,
            'member_photo_path' => null,
            'latitude' => -6.9387000,
            'longitude' => 107.7081000,
            'location_address' => 'Gedebage, Kota Bandung',
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'sync_status' => SyncStatus::Synced->value,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => MemberApprovalStatus::Pending->value,
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => MemberApprovalStatus::Approved->value,
            'approved_by' => User::factory()->admin(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => MemberApprovalStatus::Rejected->value,
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => User::factory()->admin(),
            'rejected_at' => now(),
            'rejection_reason' => 'Data pengajuan belum memenuhi ketentuan.',
        ]);
    }

    public function synced(): static
    {
        return $this->state(fn (array $attributes) => ['sync_status' => SyncStatus::Synced->value]);
    }

    public function pendingSync(): static
    {
        return $this->state(fn (array $attributes) => ['sync_status' => SyncStatus::Pending->value]);
    }

    public function fromProspect(Prospect $prospect): static
    {
        return $this->state(fn (array $attributes) => [
            'source_prospect_id' => $prospect->id,
            'marketing_profile_id' => $prospect->marketing_profile_id,
            'resort' => $prospect->resort,
        ]);
    }
}
