<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Enums\SyncStatus;
use App\Enums\TrackingStatus;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\OperationalRecapRow;
use App\Models\Prospect;
use App\Models\TrackingSession;
use App\Models\User;
use App\Models\VisitReport;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_database_aggregates(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin MMS']);
        $marketing = $this->marketingProfile('M01');
        $otherMarketing = $this->marketingProfile('M02');
        $date = '2026-07-20';

        DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'report_date' => $date,
            'day_name' => DayName::Monday->value,
            'total_target_amount' => 8000000,
            'drop_amount' => 5500000,
            'storting' => 2300000,
        ]);
        DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'report_date' => $date,
            'day_name' => DayName::Monday->value,
            'total_target_amount' => 2000000,
            'drop_amount' => 1000000,
            'storting' => 700000,
        ]);

        Member::factory()->approved()->create([
            'marketing_profile_id' => $marketing->id,
            'input_date' => $date,
            'approval_status' => MemberApprovalStatus::Approved->value,
        ]);
        Prospect::factory()->pendingSync()->create([
            'marketing_profile_id' => $marketing->id,
            'input_date' => $date,
            'sync_status' => SyncStatus::Pending->value,
        ]);
        $prospect = Prospect::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'input_date' => $date,
        ]);
        VisitReport::factory()->create([
            'prospect_id' => $prospect->id,
            'marketing_profile_id' => $marketing->id,
            'visit_date' => $date,
            'day_name' => DayName::Monday->value,
        ]);
        MarketingSchedule::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'schedule_date' => $date,
            'day_name' => DayName::Monday->value,
            'created_by' => $admin->id,
            'agenda' => 'Presentasi produk tabungan',
        ]);
        TrackingSession::factory()->active()->create([
            'marketing_profile_id' => $marketing->id,
            'session_date' => $date,
            'day_name' => DayName::Monday->value,
            'status' => TrackingStatus::Active->value,
        ]);

        $recap = OperationalRecap::factory()->create([
            'recap_date' => $date,
            'day_name' => DayName::Monday->value,
            'created_by' => $admin->id,
        ]);
        OperationalRecapRow::factory()->create([
            'operational_recap_id' => $recap->id,
            'marketing_profile_id' => $marketing->id,
            'mg' => 'M01',
            'target_s' => 8000000,
            'drop_total' => 5500000,
            'storting_total' => 2300000,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard?date=2026-07-20&day=Senin');

        $response->assertOk();
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Drop');
        $response->assertSee('Storting');
        $response->assertSee('Sirkulasi');
        $response->assertSee('Target Masuk');
        $response->assertSee('Target Keluar');
        $response->assertSee('Total Target');
        $response->assertSee('Anggota Masuk');
        $response->assertSee('Anggota Keluar');
        $response->assertSee('Belum tersedia');
        $response->assertSee('Foto Pencairan');
        $response->assertSee('Foto Bukti Transfer');
        $response->assertSee('PDL');
        $response->assertSee('Tracking PDL');
        $response->assertDontSee('Status Marketing');
        $response->assertSee('Rp 10.000.000');
        $response->assertSee('Rp 6.500.000');
        $response->assertSee('Rp 3.000.000');
        $response->assertSee('M01');
    }

    public function test_dashboard_uses_global_period_totals(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile('M01');
        $otherMarketing = $this->marketingProfile('M02');

        DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'total_target_amount' => 8000000,
            'drop_amount' => 5500000,
            'storting' => 2300000,
        ]);
        DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $otherMarketing->id,
            'total_target_amount' => 2000000,
            'drop_amount' => 1000000,
            'storting' => 700000,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard?date=2026-07-20&day=Senin&marketing_id='.$marketing->id);

        $response->assertOk();
        $response->assertSee('Rp 10.000.000');
    }

    public function test_dashboard_empty_state_and_admin_alias_are_safe(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin MMS']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard Admin');

        $this->actingAs($admin)
            ->get('/admin/dashboard?date=2026-07-21&day=Selasa')
            ->assertOk()
            ->assertSee('Belum ada PDL')
            ->assertSee('Belum ada foto pencairan')
            ->assertSee('Belum ada foto bukti transfer');
    }

    public function test_dashboard_defaults_to_the_latest_operational_report_date(): void
    {
        CarbonImmutable::setTestNow('2026-07-26 10:00:00');

        try {
            $admin = User::factory()->admin()->create();
            $marketing = $this->marketingProfile('M01');

            DailyOperationalReport::factory()->create([
                'marketing_profile_id' => $marketing->id,
                'report_date' => '2026-07-25',
                'day_name' => DayName::Saturday->value,
                'total_target_amount' => 8000000,
            ]);

            $this->actingAs($admin)
                ->get('/admin/dashboard')
                ->assertOk()
                ->assertSee('Sabtu, 25 Juli 2026')
                ->assertSee('Rp 8.000.000');
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    private function marketingProfile(string $code): MarketingProfile
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

        $profile->workDays()->create([
            'day_name' => DayName::Monday->value,
        ]);

        return $profile;
    }
}
