<?php

namespace Database\Factories;

use App\Enums\DayName;
use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use App\Enums\VisitResult;
use App\Models\Prospect;
use App\Models\VisitReport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VisitReport>
 */
class VisitReportFactory extends Factory
{
    protected $model = VisitReport::class;

    public function definition(): array
    {
        return [
            'local_uuid' => (string) Str::uuid(),
            'prospect_id' => Prospect::factory(),
            'marketing_profile_id' => fn (array $attributes) => Prospect::query()->find($attributes['prospect_id'])->marketing_profile_id,
            'visit_date' => '2026-07-20',
            'visit_time' => '09:30:00',
            'day_name' => DayName::Monday->value,
            'visit_purpose' => 'Presentasi produk tabungan',
            'visit_result' => VisitResult::Met->value,
            'prospect_status' => ProspectStatus::Interested->value,
            'notes' => 'Prospek bersedia menerima follow up.',
            'follow_up_date' => '2026-07-22',
            'photo_path' => 'visit-reports/development/visit-reference.webp',
            'photo_caption' => 'Foto kunjungan development',
            'resort' => 'Gedebage',
            'latitude' => -6.9388000,
            'longitude' => 107.7079000,
            'location_address' => 'Gedebage, Kota Bandung',
            'sync_status' => SyncStatus::Synced->value,
        ];
    }

    public function met(): static
    {
        return $this->state(fn (array $attributes) => ['visit_result' => VisitResult::Met->value]);
    }

    public function notMet(): static
    {
        return $this->state(fn (array $attributes) => ['visit_result' => VisitResult::NotMet->value]);
    }

    public function transactionCompleted(): static
    {
        return $this->state(fn (array $attributes) => ['visit_result' => VisitResult::TransactionCompleted->value]);
    }
}
