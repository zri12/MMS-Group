<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Enums\ProspectStatus;
use App\Enums\ScheduleStatus;
use App\Enums\SyncStatus;
use App\Enums\TrackingPointType;
use App\Enums\TrackingStatus;
use App\Enums\UserRole;
use App\Enums\VisitResult;
use App\Models\DailyOperationalReport;
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
use Tests\TestCase;

class ModelCastTest extends TestCase
{
    public function test_user_casts_role_and_runtime_fields(): void
    {
        $casts = (new User)->getCasts();

        $this->assertSame(UserRole::class, $casts['role']);
        $this->assertSame('boolean', $casts['is_active']);
        $this->assertSame('datetime', $casts['last_login_at']);
        $this->assertSame('hashed', $casts['password']);
    }

    public function test_domain_status_casts_use_backed_enums(): void
    {
        $this->assertSame(MemberApprovalStatus::class, (new Member)->getCasts()['approval_status']);
        $this->assertSame(ProspectStatus::class, (new Prospect)->getCasts()['status']);
        $this->assertSame(DayName::class, (new DailyOperationalReport)->getCasts()['day_name']);
        $this->assertSame(DayName::class, (new MarketingWorkDay)->getCasts()['day_name']);
        $this->assertSame(DayName::class, (new MarketingSchedule)->getCasts()['day_name']);
        $this->assertSame(ScheduleStatus::class, (new MarketingSchedule)->getCasts()['status']);
        $this->assertSame(DayName::class, (new VisitReport)->getCasts()['day_name']);
        $this->assertSame(VisitResult::class, (new VisitReport)->getCasts()['visit_result']);
        $this->assertSame(ProspectStatus::class, (new VisitReport)->getCasts()['prospect_status']);
        $this->assertSame(DayName::class, (new TrackingSession)->getCasts()['day_name']);
        $this->assertSame(TrackingStatus::class, (new TrackingSession)->getCasts()['status']);
        $this->assertSame(TrackingPointType::class, (new TrackingPoint)->getCasts()['point_type']);
        $this->assertSame(DayName::class, (new OperationalRecap)->getCasts()['day_name']);
        $this->assertSame(SyncStatus::class, (new DailyOperationalReport)->getCasts()['sync_status']);
    }

    public function test_date_datetime_and_decimal_casts_are_declared(): void
    {
        $this->assertSame('date', (new Prospect)->getCasts()['input_date']);
        $this->assertSame('decimal:7', (new Prospect)->getCasts()['latitude']);
        $this->assertSame('date', (new VisitReport)->getCasts()['follow_up_date']);
        $this->assertSame('datetime', (new TrackingSession)->getCasts()['started_at']);
        $this->assertSame('datetime', (new TrackingPoint)->getCasts()['recorded_at']);
        $this->assertSame('datetime', (new TrackingPoint)->getCasts()['created_at']);
        $this->assertSame('decimal:2', (new OperationalRecapRow)->getCasts()['percentage']);
    }
}
