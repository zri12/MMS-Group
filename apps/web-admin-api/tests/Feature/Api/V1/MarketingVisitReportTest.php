<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\DayName;
use App\Enums\ProspectStatus;
use App\Enums\VisitResult;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use App\Models\User;
use App\Models\VisitReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketingVisitReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_list_only_own_visit_reports(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $prospect = Prospect::factory()->create(['marketing_profile_id' => $marketing->id, 'name' => 'Ahmad Hidayat']);
        $otherProspect = Prospect::factory()->create(['marketing_profile_id' => $otherMarketing->id, 'name' => 'Prospek Lain']);

        VisitReport::factory()->create([
            'prospect_id' => $prospect->id,
            'marketing_profile_id' => $marketing->id,
            'visit_result' => VisitResult::Met->value,
        ]);
        VisitReport::factory()->create([
            'prospect_id' => $otherProspect->id,
            'marketing_profile_id' => $otherMarketing->id,
            'visit_result' => VisitResult::Met->value,
        ]);

        $response = $this->withToken($token)->getJson('/api/v1/visit-reports?prospect_id='.$prospect->id.'&result=Berhasil%20Bertemu&per_page=5');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonPath('data.0.prospect.name', 'Ahmad Hidayat');
        $response->assertJsonPath('data.0.marketing.id', $marketing->id);
    }

    public function test_marketing_can_create_visit_report_idempotently_and_updates_prospect(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $prospect = Prospect::factory()->newStatus()->create(['marketing_profile_id' => $marketing->id]);
        $uuid = (string) Str::uuid();
        $payload = $this->payload([
            'local_uuid' => $uuid,
            'prospect_id' => $prospect->id,
            'prospect_status' => ProspectStatus::Interested->value,
        ]);

        $first = $this->withToken($token)->postJson('/api/v1/visit-reports', $payload);
        $second = $this->withToken($token)->postJson('/api/v1/visit-reports', $payload);

        $first->assertCreated();
        $second->assertCreated();
        $this->assertSame($first->json('data.id'), $second->json('data.id'));
        $this->assertDatabaseCount('visit_reports', 1);
        $this->assertDatabaseHas('visit_reports', [
            'marketing_profile_id' => $marketing->id,
            'prospect_id' => $prospect->id,
            'local_uuid' => $uuid,
            'prospect_status' => ProspectStatus::Interested->value,
        ]);
        $this->assertSame(ProspectStatus::Interested, $prospect->refresh()->status);
    }

    public function test_visit_report_prospect_must_belong_to_same_marketing(): void
    {
        [, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $otherProspect = Prospect::factory()->create(['marketing_profile_id' => $otherMarketing->id]);

        $this->withToken($token)
            ->postJson('/api/v1/visit-reports', $this->payload(['prospect_id' => $otherProspect->id]))
            ->assertNotFound();
    }

    public function test_marketing_can_show_only_own_visit_report(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $ownProspect = Prospect::factory()->create(['marketing_profile_id' => $marketing->id]);
        $otherProspect = Prospect::factory()->create(['marketing_profile_id' => $otherMarketing->id]);
        $own = VisitReport::factory()->create([
            'prospect_id' => $ownProspect->id,
            'marketing_profile_id' => $marketing->id,
        ]);
        $other = VisitReport::factory()->create([
            'prospect_id' => $otherProspect->id,
            'marketing_profile_id' => $otherMarketing->id,
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/visit-reports/'.$own->id)
            ->assertOk()
            ->assertJsonPath('data.id', $own->id);

        $this->withToken($token)
            ->getJson('/api/v1/visit-reports/'.$other->id)
            ->assertNotFound();
    }

    public function test_visit_report_validation_rejects_invalid_coordinates_without_accept_header(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $prospect = Prospect::factory()->create(['marketing_profile_id' => $marketing->id]);

        $response = $this->withToken($token)->post('/api/v1/visit-reports', $this->payload([
            'prospect_id' => $prospect->id,
            'latitude' => 91,
            'longitude' => 181,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    public function test_duplicate_visit_report_local_uuid_from_other_marketing_is_conflict(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $prospect = Prospect::factory()->create(['marketing_profile_id' => $marketing->id]);
        $otherProspect = Prospect::factory()->create(['marketing_profile_id' => $otherMarketing->id]);
        $uuid = (string) Str::uuid();

        VisitReport::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'prospect_id' => $otherProspect->id,
            'local_uuid' => $uuid,
        ]);

        $this->withToken($token)
            ->postJson('/api/v1/visit-reports', $this->payload([
                'local_uuid' => $uuid,
                'prospect_id' => $prospect->id,
            ]))
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
            'username' => strtolower($code).'.visitreport',
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
            'prospect_id' => 1,
            'visit_purpose' => 'Presentasi produk tabungan',
            'visit_result' => VisitResult::Met->value,
            'prospect_status' => ProspectStatus::Interested->value,
            'notes' => 'Prospek bersedia menerima follow up.',
            'follow_up_date' => '2026-07-22',
            'photo_caption' => 'Foto kunjungan',
            'resort' => 'Gedebage',
            'day' => DayName::Monday->value,
            'date' => '2026-07-20',
            'time' => '09:30:00',
            'latitude' => -6.9388,
            'longitude' => 107.7079,
            'location_address' => 'Gedebage, Kota Bandung',
            ...$overrides,
        ];
    }
}
