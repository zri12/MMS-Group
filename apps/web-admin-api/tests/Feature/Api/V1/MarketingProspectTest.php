<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\ProspectStatus;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketingProspectTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_list_only_own_prospects(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');

        Prospect::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Ahmad Hidayat',
            'status' => ProspectStatus::New->value,
        ]);
        Prospect::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'name' => 'Prospek Lain',
        ]);

        $response = $this->withToken($token)->getJson('/api/v1/prospects?status=Baru&search=Ahmad&per_page=5');

        $response->assertOk();
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonPath('data.0.name', 'Ahmad Hidayat');
        $response->assertJsonPath('data.0.marketing_id', null);
        $this->assertSame($marketing->id, $response->json('data.0.marketing.id'));
    }

    public function test_marketing_can_create_prospect_idempotently(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $uuid = (string) Str::uuid();
        $payload = $this->payload(['local_uuid' => $uuid]);

        $first = $this->withToken($token)->postJson('/api/v1/prospects', $payload);
        $second = $this->withToken($token)->postJson('/api/v1/prospects', $payload);

        $first->assertCreated();
        $second->assertCreated();
        $this->assertSame($first->json('data.id'), $second->json('data.id'));
        $this->assertDatabaseCount('prospects', 1);
        $this->assertDatabaseHas('prospects', [
            'marketing_profile_id' => $marketing->id,
            'local_uuid' => $uuid,
            'name' => 'Ahmad Hidayat',
        ]);
    }

    public function test_marketing_can_show_and_update_only_own_prospect(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $own = Prospect::factory()->create(['marketing_profile_id' => $marketing->id]);
        $other = Prospect::factory()->create(['marketing_profile_id' => $otherMarketing->id]);

        $this->withToken($token)
            ->getJson('/api/v1/prospects/'.$own->id)
            ->assertOk()
            ->assertJsonPath('data.id', $own->id);

        $this->withToken($token)
            ->putJson('/api/v1/prospects/'.$own->id, [
                'status' => ProspectStatus::Interested->value,
                'notes' => 'Perlu kunjungan ulang.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', ProspectStatus::Interested->value);

        $this->assertDatabaseHas('prospects', [
            'id' => $own->id,
            'status' => ProspectStatus::Interested->value,
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/prospects/'.$other->id)
            ->assertNotFound();
    }

    public function test_prospect_validation_rejects_invalid_coordinates_without_accept_header(): void
    {
        [, $token] = $this->marketingToken('M01');

        $response = $this->withToken($token)->post('/api/v1/prospects', $this->payload([
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

        Prospect::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'local_uuid' => $uuid,
        ]);

        $this->withToken($token)
            ->postJson('/api/v1/prospects', $this->payload(['local_uuid' => $uuid]))
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
            'username' => strtolower($code).'.marketing',
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
            'name' => 'Ahmad Hidayat',
            'phone' => '081211112201',
            'address' => 'Jl. Gedebage Selatan No. 21',
            'business' => 'Toko Kelontong',
            'status' => ProspectStatus::New->value,
            'initial_visit_result' => 'Bersedia menerima presentasi produk',
            'notes' => 'Tertarik produk tabungan usaha.',
            'resort' => 'Gedebage',
            'input_date' => '2026-07-20',
            'input_time' => '08:30:00',
            'latitude' => -6.9388,
            'longitude' => 107.7079,
            'location_address' => 'Gedebage, Kota Bandung',
            ...$overrides,
        ];
    }
}
