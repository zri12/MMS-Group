<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\DayName;
use App\Enums\ScheduleStatus;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_list_requires_marketing_token(): void
    {
        $this->get('/api/v1/schedules')
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'Autentikasi diperlukan.',
            ]);

        $admin = User::factory()->admin()->create();
        $adminToken = $admin->createToken('admin', ['marketing-mobile'])->plainTextToken;

        $this->withToken($adminToken)
            ->get('/api/v1/schedules')
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'Akses tidak tersedia.',
            ]);
    }

    public function test_marketing_can_list_only_own_schedules_with_pagination(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');

        MarketingSchedule::factory()->count(2)->create([
            'marketing_profile_id' => $marketing->id,
            'schedule_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
            'status' => ScheduleStatus::NotVisited->value,
        ]);
        MarketingSchedule::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'schedule_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
        ]);

        $response = $this->withToken($token)->getJson('/api/v1/schedules?date=2026-07-20&day=Senin&status=Belum%20Dikunjungi&per_page=1');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Jadwal berhasil dimuat.',
            'meta' => [
                'current_page' => 1,
                'per_page' => 1,
                'total' => 2,
            ],
        ]);
        $this->assertCount(1, $response->json('data'));
        $this->assertSame($marketing->id, $response->json('data.0.marketing_id'));
    }

    public function test_today_schedule_uses_server_date(): void
    {
        CarbonImmutable::setTestNow('2026-07-20 08:00:00');
        [$marketing, $token] = $this->marketingToken('M01');

        MarketingSchedule::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'schedule_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
            'agenda' => 'Kunjungan hari ini',
        ]);
        MarketingSchedule::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'schedule_date' => '2026-07-21',
            'day_name' => DayName::Tuesday->value,
            'agenda' => 'Kunjungan besok',
        ]);

        $response = $this->withToken($token)->getJson('/api/v1/schedules/today');

        $response->assertOk();
        $response->assertJsonPath('data.0.agenda', 'Kunjungan hari ini');
        $this->assertCount(1, $response->json('data'));
        CarbonImmutable::setTestNow();
    }

    public function test_marketing_can_show_only_own_schedule(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        [$otherMarketing] = $this->marketingToken('M02');
        $own = MarketingSchedule::factory()->create(['marketing_profile_id' => $marketing->id]);
        $other = MarketingSchedule::factory()->create(['marketing_profile_id' => $otherMarketing->id]);

        $this->withToken($token)
            ->getJson('/api/v1/schedules/'.$own->id)
            ->assertOk()
            ->assertJsonPath('data.id', $own->id);

        $this->withToken($token)
            ->getJson('/api/v1/schedules/'.$other->id)
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ]);
    }

    public function test_marketing_can_update_schedule_status_with_allowed_transition(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $schedule = MarketingSchedule::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'status' => ScheduleStatus::NotVisited->value,
        ]);

        $response = $this->withToken($token)->patchJson('/api/v1/schedules/'.$schedule->id.'/status', [
            'status' => ScheduleStatus::InProgress->value,
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.status', ScheduleStatus::InProgress->value);
        $this->assertDatabaseHas('marketing_schedules', [
            'id' => $schedule->id,
            'status' => ScheduleStatus::InProgress->value,
        ]);
    }

    public function test_invalid_schedule_status_transition_returns_validation_error(): void
    {
        [$marketing, $token] = $this->marketingToken('M01');
        $schedule = MarketingSchedule::factory()->completed()->create([
            'marketing_profile_id' => $marketing->id,
        ]);

        $response = $this->withToken($token)->patch('/api/v1/schedules/'.$schedule->id.'/status', [
            'status' => ScheduleStatus::InProgress->value,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('status');
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
}
