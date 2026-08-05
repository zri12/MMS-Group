<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    public function test_user_and_marketing_profile_relationships(): void
    {
        $this->assertInstanceOf(HasOne::class, (new User)->marketingProfile());
        $this->assertInstanceOf(BelongsTo::class, (new MarketingProfile)->user());
        $this->assertInstanceOf(HasMany::class, (new MarketingProfile)->workDays());
        $this->assertInstanceOf(HasMany::class, (new MarketingProfile)->prospects());
        $this->assertInstanceOf(HasMany::class, (new MarketingProfile)->members());
    }

    public function test_prospect_member_and_visit_relationships(): void
    {
        $this->assertInstanceOf(BelongsTo::class, (new Prospect)->marketingProfile());
        $this->assertInstanceOf(HasMany::class, (new Prospect)->visitReports());
        $this->assertInstanceOf(HasOne::class, (new Prospect)->member());
        $this->assertInstanceOf(BelongsTo::class, (new Member)->sourceProspect());
        $this->assertInstanceOf(BelongsTo::class, (new VisitReport)->prospect());
    }

    public function test_schedule_report_tracking_and_recap_relationships(): void
    {
        $this->assertInstanceOf(BelongsTo::class, (new MarketingWorkDay)->marketingProfile());
        $this->assertInstanceOf(BelongsTo::class, (new MarketingSchedule)->marketingProfile());
        $this->assertInstanceOf(BelongsTo::class, (new DailyOperationalReport)->marketingProfile());
        $this->assertInstanceOf(BelongsTo::class, (new TrackingSession)->marketingProfile());
        $this->assertInstanceOf(BelongsTo::class, (new TrackingSession)->schedule());
        $this->assertInstanceOf(HasMany::class, (new TrackingSession)->points());
        $this->assertInstanceOf(BelongsTo::class, (new TrackingPoint)->trackingSession());
        $this->assertInstanceOf(HasMany::class, (new OperationalRecap)->rows());
        $this->assertInstanceOf(BelongsTo::class, (new OperationalRecapRow)->operationalRecap());
        $this->assertInstanceOf(BelongsTo::class, (new OperationalRecapRow)->marketingProfile());
    }
}
