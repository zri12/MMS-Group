<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\DayName;
use App\Enums\TrackingStatus;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_tracking_map_and_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile('M01');
        $schedule = MarketingSchedule::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'schedule_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
        ]);
        $session = TrackingSession::factory()->active()->forSchedule($schedule)->create([
            'marketing_profile_id' => $marketing->id,
            'session_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
            'status' => TrackingStatus::Active->value,
        ]);
        TrackingPoint::factory()->create([
            'tracking_session_id' => $session->id,
            'latitude' => -6.9388,
            'longitude' => 107.7079,
            'recorded_at' => '2026-07-20 08:30:00',
        ]);

        $this->actingAs($admin)
            ->get('/admin/tracking?date=2026-07-20&status=Aktif')
            ->assertOk()
            ->assertSee('Tracking PDL')
            ->assertSee('data-tracking-map', false)
            ->assertSee('M01 - Marketing M01')
            ->assertSee('Detail Tracking');

        $this->actingAs($admin)
            ->get(route('admin.tracking.show', $session))
            ->assertOk()
            ->assertSee('Detail Tracking')
            ->assertSee('-6.9388000')
            ->assertSee('Titik Tracking');
    }

    public function test_admin_tracking_distinguishes_not_started_and_not_scheduled(): void
    {
        $admin = User::factory()->admin()->create();
        $scheduledMarketing = $this->marketingProfile('M01');
        $notScheduledMarketing = $this->marketingProfile('M02');

        MarketingSchedule::factory()->create([
            'marketing_profile_id' => $scheduledMarketing->id,
            'schedule_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
        ]);

        $this->actingAs($admin)
            ->get('/admin/tracking?date=2026-07-20&status=Belum%20Mulai')
            ->assertOk()
            ->assertSee('M01 - Marketing M01')
            ->assertSee('Belum Mulai');

        $this->actingAs($admin)
            ->get('/admin/tracking?date=2026-07-20&status=Tidak%20Dijadwalkan')
            ->assertOk()
            ->assertSee('M02 - Marketing M02')
            ->assertSee('Tidak Dijadwalkan');

        $this->assertSame('M02', $notScheduledMarketing->code);
    }

    public function test_tracking_feed_returns_safe_marker_payload_for_polling(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile('M01');
        $session = TrackingSession::factory()->active()->create([
            'marketing_profile_id' => $marketing->id,
            'session_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
            'status' => TrackingStatus::Active->value,
        ]);
        TrackingPoint::factory()->create([
            'tracking_session_id' => $session->id,
            'latitude' => -6.9388,
            'longitude' => 107.7079,
            'recorded_at' => '2026-07-20 08:30:00',
        ]);

        $this->actingAs($admin)
            ->getJson('/admin/tracking/feed?date=2026-07-20&status=Aktif')
            ->assertOk()
            ->assertJsonPath('data.markers.0.label', 'M01 - Marketing M01')
            ->assertJsonPath('data.markers.0.status', 'Aktif')
            ->assertJsonPath('data.markers.0.lat', -6.9388)
            ->assertJsonPath('data.markers.0.lng', 107.7079);
    }

    public function test_tracking_ui_uses_feed_polling_without_full_page_reload(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/tracking');
        $mapScript = file_get_contents(resource_path('js/maps/index.js'));

        $response->assertOk()
            ->assertSee('data-feed-url=', false);

        $this->assertIsString($mapScript);
        $this->assertStringNotContainsString('window.location.reload', $mapScript);
        $this->assertStringNotContainsString('location.reload', $mapScript);
    }

    public function test_guest_and_marketing_cannot_open_admin_tracking(): void
    {
        $marketingUser = User::factory()->marketing()->create();

        $this->get('/admin/tracking')->assertRedirect(route('login', absolute: false));

        $this->actingAs($marketingUser)
            ->get('/admin/tracking')
            ->assertForbidden();
    }

    private function marketingProfile(string $code): MarketingProfile
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

        $profile->workDays()->create(['day_name' => DayName::Monday->value]);

        return $profile;
    }
}
