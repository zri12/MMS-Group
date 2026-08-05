<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Enums\DayName;
use App\Enums\TrackingPointType;
use App\Enums\TrackingStatus;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\MarketingWorkDay;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\OperationalRecapRow;
use App\Models\Prospect;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use App\Models\User;
use App\Models\VisitReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_domain_factories_create_valid_records(): void
    {
        $user = User::factory()->marketing()->create();
        $profile = MarketingProfile::factory()->create(['user_id' => $user->id]);
        $workDay = MarketingWorkDay::factory()->create([
            'marketing_profile_id' => $profile->id,
            'day_name' => DayName::Monday,
        ]);
        $prospect = Prospect::factory()->create(['marketing_profile_id' => $profile->id]);
        $member = Member::factory()->create([
            'marketing_profile_id' => $profile->id,
            'source_prospect_id' => $prospect->id,
        ]);
        $schedule = MarketingSchedule::factory()->create([
            'marketing_profile_id' => $profile->id,
            'prospect_id' => $prospect->id,
        ]);
        $dailyReport = DailyOperationalReport::factory()->create(['marketing_profile_id' => $profile->id]);
        $visitReport = VisitReport::factory()->create([
            'prospect_id' => $prospect->id,
            'marketing_profile_id' => $profile->id,
        ]);
        $trackingSession = TrackingSession::factory()->create([
            'marketing_profile_id' => $profile->id,
            'schedule_id' => $schedule->id,
        ]);
        $trackingPoint = TrackingPoint::factory()->create([
            'tracking_session_id' => $trackingSession->id,
            'point_type' => TrackingPointType::Visit,
        ]);
        $recap = OperationalRecap::factory()->create();
        $recapRow = OperationalRecapRow::factory()->create([
            'operational_recap_id' => $recap->id,
            'marketing_profile_id' => $profile->id,
        ]);

        $this->assertTrue($profile->user->is($user));
        $this->assertTrue($prospect->member->is($member));
        $this->assertTrue($workDay->day_name === DayName::Monday);
        $this->assertTrue($trackingPoint->point_type === TrackingPointType::Visit);
        $this->assertTrue($trackingSession->points->contains($trackingPoint));
        $this->assertTrue($recap->rows->contains($recapRow));
        $this->assertTrue($dailyReport->marketingProfile->is($profile));
        $this->assertTrue($visitReport->prospect->is($prospect));
    }

    public function test_factories_keep_cross_model_relationships_consistent(): void
    {
        $profile = MarketingProfile::factory()->create();
        $prospect = Prospect::factory()->create(['marketing_profile_id' => $profile->id]);

        $schedule = MarketingSchedule::factory()->forProspect($prospect)->create();
        $trackingSession = TrackingSession::factory()->forSchedule($schedule)->offline()->create();

        $this->assertSame($profile->id, $schedule->marketing_profile_id);
        $this->assertSame($profile->id, $schedule->prospect->marketing_profile_id);
        $this->assertSame($schedule->marketing_profile_id, $trackingSession->marketing_profile_id);
        $this->assertSame($schedule->id, $trackingSession->schedule_id);
        $this->assertTrue($trackingSession->status === TrackingStatus::Offline);
        $this->assertNotNull($trackingSession->ended_at);
        $this->assertFalse(method_exists(TrackingSession::factory(), 'completed'));
    }

    public function test_operational_recap_factory_can_create_more_than_one_record_with_matching_day_names(): void
    {
        $recaps = OperationalRecap::factory()->count(2)->create();

        $this->assertSame(2, OperationalRecap::query()->count());
        $this->assertSame(2, $recaps->pluck('recap_date')->unique(fn ($date): string => $date->format('Y-m-d'))->count());

        foreach ($recaps as $recap) {
            $dayOfWeek = (int) $recap->recap_date->format('N');

            $this->assertNotSame(7, $dayOfWeek);
            $this->assertTrue($recap->day_name === $this->dayNameFor($dayOfWeek));
        }
    }

    private function dayNameFor(int $dayOfWeek): DayName
    {
        return match ($dayOfWeek) {
            1 => DayName::Monday,
            2 => DayName::Tuesday,
            3 => DayName::Wednesday,
            4 => DayName::Thursday,
            5 => DayName::Friday,
            6 => DayName::Saturday,
        };
    }
}
