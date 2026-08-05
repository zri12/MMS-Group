<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\MemberApprovalStatus;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketingMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_list_only_own_members(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');

        Member::factory()->pending()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Siti Aminah',
            'member_number' => 'AGT-101',
        ]);
        Member::factory()->pending()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'name' => 'Anggota Lain',
        ]);

        $response = $this->withToken($token)->getJson('/api/v1/members?approval_status=Menunggu&search=Siti&per_page=5');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonPath('data.0.name', 'Siti Aminah');
        $response->assertJsonPath('data.0.marketing.id', $marketing->id);
    }

    public function test_marketing_can_create_member_idempotently(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $prospect = Prospect::factory()->create(['marketing_profile_id' => $marketing->id]);
        $uuid = (string) Str::uuid();
        $payload = $this->payload([
            'local_uuid' => $uuid,
            'source_prospect_id' => $prospect->id,
            'approval_status' => MemberApprovalStatus::Approved->value,
            'marketing_profile_id' => 999,
            'approved_by' => 999,
        ]);

        $first = $this->withToken($token)->postJson('/api/v1/members', $payload);
        $second = $this->withToken($token)->postJson('/api/v1/members', $payload);

        $first->assertCreated();
        $second->assertCreated();
        $this->assertSame($first->json('data.id'), $second->json('data.id'));
        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseHas('members', [
            'marketing_profile_id' => $marketing->id,
            'source_prospect_id' => $prospect->id,
            'local_uuid' => $uuid,
            'approval_status' => MemberApprovalStatus::Pending->value,
            'approved_by' => null,
        ]);
    }

    public function test_member_source_prospect_must_belong_to_same_marketing(): void
    {
        [, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $otherProspect = Prospect::factory()->create(['marketing_profile_id' => $otherMarketing->id]);

        $this->withToken($token)
            ->postJson('/api/v1/members', $this->payload(['source_prospect_id' => $otherProspect->id]))
            ->assertNotFound();
    }

    public function test_marketing_can_show_only_own_member(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $own = Member::factory()->create(['marketing_profile_id' => $marketing->id]);
        $other = Member::factory()->create(['marketing_profile_id' => $otherMarketing->id]);

        $this->withToken($token)
            ->getJson('/api/v1/members/'.$own->id)
            ->assertOk()
            ->assertJsonPath('data.id', $own->id);

        $this->withToken($token)
            ->getJson('/api/v1/members/'.$other->id)
            ->assertNotFound();
    }

    public function test_member_validation_rejects_invalid_coordinates_without_accept_header(): void
    {
        [, $token] = $this->marketingToken('M01');

        $response = $this->withToken($token)->post('/api/v1/members', $this->payload([
            'latitude' => 91,
            'longitude' => 181,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    public function test_duplicate_local_uuid_from_other_marketing_is_conflict(): void
    {
        [, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $uuid = (string) Str::uuid();

        Member::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'local_uuid' => $uuid,
            'member_number' => 'AGT-777',
            'loan_number' => 'PNJ-777',
        ]);

        $this->withToken($token)
            ->postJson('/api/v1/members', $this->payload([
                'local_uuid' => $uuid,
                'member_number' => 'AGT-777',
                'loan_number' => 'PNJ-777',
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
            'username' => strtolower($code).'.member',
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
            'source_prospect_id' => null,
            'resort' => 'Gedebage',
            'date' => '2026-07-20',
            'time' => '09:15:00',
            'name' => 'Siti Aminah',
            'member_number' => 'AGT-001',
            'loan_number' => 'PNJ-001',
            'address' => 'Jl. Gedebage Selatan No. 22',
            'phone' => '081211112202',
            'business' => 'Warung Sembako',
            'loan_amount' => 5000000,
            'installment_amount' => 250000,
            'insurance_amount' => 100000,
            'collateral' => 'BPKB motor',
            'latitude' => -6.9387,
            'longitude' => 107.7081,
            'location_address' => 'Gedebage, Kota Bandung',
            ...$overrides,
        ];
    }
}
