<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\DayName;
use App\Enums\TrackingPointType;
use App\Enums\TrackingStatus;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketingTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        config(['mms.testing.allow_sunday_operations' => false]);

        parent::tearDown();
    }

    public function test_marketing_can_start_tracking_idempotently_and_read_current_session(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $schedule = MarketingSchedule::factory()->create(['marketing_profile_id' => $marketing->id]);
        $uuid = (string) Str::uuid();
        $payload = [
            'local_uuid' => $uuid,
            'schedule_id' => $schedule->id,
            'started_at' => '2026-07-20T08:05:00+07:00',
        ];

        $first = $this->withToken($token)->postJson('/api/v1/tracking/sessions/start', $payload);
        $second = $this->withToken($token)->postJson('/api/v1/tracking/sessions/start', $payload);

        $first->assertCreated();
        $second->assertCreated();
        $this->assertSame($first->json('data.session_id'), $second->json('data.session_id'));
        $this->assertDatabaseCount('tracking_sessions', 1);
        $this->assertDatabaseHas('tracking_sessions', [
            'marketing_profile_id' => $marketing->id,
            'local_uuid' => $uuid,
            'status' => TrackingStatus::Active->value,
            'day_name' => DayName::Monday->value,
        ]);

        $this->withToken($token)
            ->getJson('/api/v1/tracking/sessions/current')
            ->assertOk()
            ->assertJsonPath('data.id', $first->json('data.session_id'));
    }

    public function test_marketing_can_start_tracking_on_sunday_when_test_mode_is_enabled(): void
    {
        config(['mms.testing.allow_sunday_operations' => true]);
        [$marketing, $token] = $this->marketingToken('M01');

        $this->withToken($token)
            ->postJson('/api/v1/tracking/sessions/start', [
                'local_uuid' => (string) Str::uuid(),
                'started_at' => '2026-08-30T08:05:00+07:00',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('tracking_sessions', [
            'marketing_profile_id' => $marketing->id,
            'day_name' => DayName::Sunday->value,
        ]);
    }

    public function test_marketing_can_store_tracking_points_idempotently(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $session = TrackingSession::factory()->active()->create(['marketing_profile_id' => $marketing->id]);
        $payload = [
            'points' => [
                $this->pointPayload(['local_uuid' => (string) Str::uuid(), 'recorded_at' => '2026-07-20T08:06:00+07:00']),
                $this->pointPayload(['local_uuid' => (string) Str::uuid(), 'recorded_at' => '2026-07-20T08:07:00+07:00']),
            ],
        ];

        $first = $this->withToken($token)->postJson('/api/v1/tracking/sessions/'.$session->id.'/points/batch', $payload);
        $second = $this->withToken($token)->postJson('/api/v1/tracking/sessions/'.$session->id.'/points/batch', $payload);

        $first->assertCreated();
        $first->assertJsonPath('data.created_count', 2);
        $first->assertJsonPath('data.duplicate_count', 0);
        $second->assertCreated();
        $second->assertJsonPath('data.created_count', 0);
        $second->assertJsonPath('data.duplicate_count', 2);
        $this->assertDatabaseCount('tracking_points', 2);
    }

    public function test_marketing_can_store_single_point_and_stop_session(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $session = TrackingSession::factory()->active()->create([
            'marketing_profile_id' => $marketing->id,
            'started_at' => '2026-07-20 08:05:00',
        ]);

        $this->withToken($token)
            ->postJson('/api/v1/tracking/sessions/'.$session->id.'/points', $this->pointPayload())
            ->assertCreated()
            ->assertJsonPath('data.created_count', 1);

        $this->withToken($token)
            ->postJson('/api/v1/tracking/sessions/'.$session->id.'/stop', [
                'ended_at' => '2026-07-20T15:30:00+07:00',
                'visit_count' => 3,
                'distance_meters' => 8400,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', TrackingStatus::Offline->value)
            ->assertJsonPath('data.distance_meters', 8400);

        $this->withToken($token)
            ->postJson('/api/v1/tracking/sessions/'.$session->id.'/stop', [
                'ended_at' => '2026-07-20T16:00:00+07:00',
                'visit_count' => 9,
                'distance_meters' => 9900,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', TrackingStatus::Offline->value)
            ->assertJsonPath('data.distance_meters', 8400);

        $this->assertDatabaseHas('tracking_sessions', [
            'id' => $session->id,
            'status' => TrackingStatus::Offline->value,
            'visit_count' => 3,
            'distance_meters' => 8400,
        ]);
    }

    public function test_marketing_cannot_start_second_active_tracking_session(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        TrackingSession::factory()->active()->create(['marketing_profile_id' => $marketing->id]);

        $this->withToken($token)
            ->post('/api/v1/tracking/sessions/start', [
                'local_uuid' => (string) Str::uuid(),
                'started_at' => '2026-07-20T08:05:00+07:00',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('tracking_session');
    }

    public function test_marketing_can_list_and_show_only_own_tracking_sessions(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $own = TrackingSession::factory()->active()->create(['marketing_profile_id' => $marketing->id]);
        $other = TrackingSession::factory()->active()->create(['marketing_profile_id' => $otherMarketing->id]);

        $this->withToken($token)
            ->getJson('/api/v1/tracking/sessions?status=Aktif&per_page=5')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $own->id);

        $this->withToken($token)
            ->getJson('/api/v1/tracking/sessions/'.$own->id)
            ->assertOk()
            ->assertJsonPath('data.session.id', $own->id);

        $this->withToken($token)
            ->getJson('/api/v1/tracking/sessions/'.$other->id)
            ->assertNotFound();
    }

    public function test_tracking_session_rejects_other_marketing_schedule(): void
    {
        [, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $otherSchedule = MarketingSchedule::factory()->create(['marketing_profile_id' => $otherMarketing->id]);

        $this->withToken($token)
            ->postJson('/api/v1/tracking/sessions/start', [
                'local_uuid' => (string) Str::uuid(),
                'schedule_id' => $otherSchedule->id,
                'started_at' => '2026-07-20T08:05:00+07:00',
            ])
            ->assertNotFound();
    }

    public function test_tracking_point_validation_rejects_invalid_coordinates_without_accept_header(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $session = TrackingSession::factory()->active()->create(['marketing_profile_id' => $marketing->id]);

        $response = $this->withToken($token)->post('/api/v1/tracking/sessions/'.$session->id.'/points/batch', [
            'points' => [$this->pointPayload(['latitude' => 91, 'longitude' => 181])],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['points.0.latitude', 'points.0.longitude']);
    }

    public function test_batch_tracking_points_rolls_back_when_one_point_conflicts(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $session = TrackingSession::factory()->active()->create(['marketing_profile_id' => $marketing->id]);
        $otherSession = TrackingSession::factory()->active()->create(['marketing_profile_id' => $marketing->id]);
        $uuid = (string) Str::uuid();

        TrackingPoint::factory()->create([
            'tracking_session_id' => $otherSession->id,
            'local_uuid' => $uuid,
        ]);

        $this->withToken($token)
            ->postJson('/api/v1/tracking/sessions/'.$session->id.'/points/batch', [
                'points' => [
                    $this->pointPayload(['local_uuid' => (string) Str::uuid(), 'recorded_at' => '2026-07-20T08:07:00+07:00']),
                    $this->pointPayload(['local_uuid' => $uuid, 'recorded_at' => '2026-07-20T08:08:00+07:00']),
                ],
            ])
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'Data tidak dapat diproses.',
            ]);

        $this->assertDatabaseCount('tracking_points', 1);
    }

    public function test_tracking_point_batch_respects_configured_maximum(): void
    {
        config(['mms.tracking.max_batch_points' => 1]);
        [$marketing, $token] = $this->marketingToken('M01');
        $session = TrackingSession::factory()->active()->create(['marketing_profile_id' => $marketing->id]);

        $this->withToken($token)
            ->post('/api/v1/tracking/sessions/'.$session->id.'/points/batch', [
                'points' => [
                    $this->pointPayload(['local_uuid' => (string) Str::uuid()]),
                    $this->pointPayload(['local_uuid' => (string) Str::uuid(), 'recorded_at' => '2026-07-20T08:07:00+07:00']),
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('points');
    }

    /**
     * @return array{0: MarketingProfile, 1: string}
     */
    private function marketingToken(string $code): array
    {
        $user = User::factory()->marketing()->create([
            'name' => 'Marketing '.$code,
            'username' => strtolower($code).'.tracking',
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
    private function pointPayload(array $overrides = []): array
    {
        return [
            'local_uuid' => (string) Str::uuid(),
            'latitude' => -6.9388,
            'longitude' => 107.7079,
            'accuracy_meters' => 8.5,
            'speed_mps' => 1.2,
            'heading' => 120,
            'altitude_meters' => 710,
            'point_type' => TrackingPointType::Journey->value,
            'recorded_at' => '2026-07-20T08:06:00+07:00',
            ...$overrides,
        ];
    }
}
