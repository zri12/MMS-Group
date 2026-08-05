<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\DayName;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketingOperationalReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_list_only_own_operational_reports(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');

        DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'resort' => 'Gedebage',
            'report_date' => '2026-07-20',
        ]);
        DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'resort' => 'Rancasari',
            'report_date' => '2026-07-20',
        ]);

        $response = $this->withToken($token)->getJson('/api/v1/operational-reports?date_from=2026-07-20&date_to=2026-07-20&per_page=5');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonPath('data.0.resort', 'Gedebage');
        $response->assertJsonPath('data.0.marketing.id', $marketing->id);
    }

    public function test_marketing_can_create_operational_report_idempotently(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $uuid = (string) Str::uuid();
        $payload = $this->payload(['local_uuid' => $uuid]);

        $first = $this->withToken($token)->postJson('/api/v1/operational-reports', $payload);
        $second = $this->withToken($token)->postJson('/api/v1/operational-reports', $payload);

        $first->assertCreated();
        $second->assertCreated();
        $this->assertSame($first->json('data.id'), $second->json('data.id'));
        $this->assertDatabaseCount('daily_operational_reports', 1);
        $this->assertDatabaseHas('daily_operational_reports', [
            'marketing_profile_id' => $marketing->id,
            'local_uuid' => $uuid,
            'drop_amount' => 5500000,
        ]);
    }

    public function test_marketing_can_show_only_own_operational_report(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $own = DailyOperationalReport::factory()->create(['marketing_profile_id' => $marketing->id]);
        $other = DailyOperationalReport::factory()->create(['marketing_profile_id' => $otherMarketing->id]);

        $this->withToken($token)
            ->getJson('/api/v1/operational-reports/'.$own->id)
            ->assertOk()
            ->assertJsonPath('data.id', $own->id);

        $this->withToken($token)
            ->getJson('/api/v1/operational-reports/'.$other->id)
            ->assertNotFound();
    }

    public function test_operational_report_validation_rejects_negative_amount_without_accept_header(): void
    {
        [, $token] = $this->marketingToken('M01');

        $response = $this->withToken($token)->post('/api/v1/operational-reports', $this->payload([
            'storting' => -1,
            'drop' => -1,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['storting', 'drop']);
    }

    public function test_duplicate_operational_report_local_uuid_from_other_marketing_is_conflict(): void
    {
        [, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $uuid = (string) Str::uuid();

        DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'local_uuid' => $uuid,
        ]);

        $this->withToken($token)
            ->postJson('/api/v1/operational-reports', $this->payload(['local_uuid' => $uuid]))
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Data tidak dapat diproses.',
            ]);
    }

    /**
     * @return array{0: MarketingProfile, 1: string}
     */
    private function marketingToken(string $code): array
    {
        $user = User::factory()->marketing()->create([
            'name' => 'Marketing '.$code,
            'username' => strtolower($code).'.opreport',
        ]);

        $profile = MarketingProfile::factory()->create([
            'user_id' => $user->id,
            'code' => $code,
            'area' => 'Gedebage',
        ]);

        return [$profile, $user->createToken('mms-marketing:Test', ['marketing-mobile'])->plainTextToken];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'local_uuid' => (string) Str::uuid(),
            'date' => '2026-07-20',
            'time' => '16:00:00',
            'day' => DayName::Monday->value,
            'resort' => 'Gedebage',
            'storting' => 2300000,
            'insurance_amount' => 150000,
            'drop' => 5500000,
            'withdrawal_saving' => 200000,
            'previous_target_amount' => 6500000,
            'previous_target_people' => 3,
            'incoming_target_amount' => 2000000,
            'incoming_target_people' => 2,
            'outgoing_target_amount' => 500000,
            'outgoing_target_people' => 1,
            'total_target_amount' => 8000000,
            'total_target_people' => 4,
            'new_drop' => 2000000,
            'continued_drop' => 3500000,
            'notes' => 'Operasional berjalan normal.',
            ...$overrides,
        ];
    }
}
