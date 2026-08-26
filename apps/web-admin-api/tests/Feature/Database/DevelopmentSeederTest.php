<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Models\Member;
use App\Models\Prospect;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use Carbon\CarbonImmutable;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class DevelopmentSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('mms.seed.default_password', 'MmsTestingOnly123!');
    }

    public function test_development_seeders_create_deterministic_domain_dataset(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 14);
        $this->assertDatabaseCount('marketing_profiles', 13);
        $this->assertDatabaseCount('marketing_work_days', 78);
        $this->assertDatabaseCount('prospects', 3);
        $this->assertDatabaseCount('members', 39);
        $this->assertDatabaseCount('marketing_schedules', 3);
        $this->assertDatabaseCount('daily_operational_reports', 13);
        $this->assertDatabaseCount('visit_reports', 1);
        $this->assertDatabaseCount('tracking_sessions', 1);
        $this->assertDatabaseCount('tracking_points', 9);
        $this->assertDatabaseCount('operational_recaps', 6);
        $this->assertDatabaseCount('operational_recap_rows', 78);

        $member = Member::query()->where('member_number', '0468')->firstOrFail();

        $this->assertNull($member->source_prospect_id);
        $this->assertNull(Prospect::query()->where('name', 'Ahmad Hidayat')->firstOrFail()->member);
        $this->assertSame(39, Member::query()->whereNull('source_prospect_id')->count());

        $this->assertSame(13, Member::query()->where('approval_status', 'Menunggu')->count());
        $this->assertSame(13, Member::query()->where('approval_status', 'Disetujui')->count());
        $this->assertSame(13, Member::query()->where('approval_status', 'Ditolak')->count());

        $this->assertSame(78, DB::table('operational_recap_rows')->whereNull('percentage')->count());
        $this->assertSame(78, DB::table('operational_recap_rows')->where('previous_circulation', 0)->count());
        $this->assertSame(78, DB::table('operational_recap_rows')->where('current_circulation', 0)->count());

        $this->assertSame(
            13,
            DB::table('marketing_profiles')
                ->join('members', 'members.marketing_profile_id', '=', 'marketing_profiles.id')
                ->select('marketing_profiles.id')
                ->groupBy('marketing_profiles.id')
                ->havingRaw('COUNT(members.id) = 3')
                ->count(),
        );

        $trackingSession = TrackingSession::query()->firstOrFail();
        $points = TrackingPoint::query()->where('tracking_session_id', $trackingSession->id)->orderBy('recorded_at')->get();

        $this->assertSame(3, $trackingSession->visit_count);
        $this->assertSame(9, $points->count());
        $this->assertSame('Mulai', $points->first()->point_type->value);
        $this->assertSame('Selesai', $points->last()->point_type->value);
        $this->assertSame(3, $points->filter(fn (TrackingPoint $point): bool => $point->point_type->value === 'Kunjungan')->count());
    }

    public function test_development_seeders_are_blocked_in_production_environment(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->expectException(RuntimeException::class);

        (new AdminSeeder)->run();
    }

    public function test_demo_seeder_creates_current_tracking_points_for_each_marketing_account(): void
    {
        CarbonImmutable::setTestNow('2026-08-26 10:00:00');

        try {
            $this->seed(DatabaseSeeder::class);
            $this->seed(DemoDataSeeder::class);

            $sessions = TrackingSession::query()
                ->whereDate('session_date', '2026-08-26')
                ->whereHas('marketingProfile.user', fn ($query) => $query->where('is_active', true))
                ->withCount('points')
                ->get();

            $this->assertCount(13, $sessions);
            $this->assertSame(13, $sessions->where('status', 'Aktif')->count() + $sessions->where('status', 'Offline')->count());
            $this->assertSame(13, $sessions->where('points_count', 1)->count());
        } finally {
            CarbonImmutable::setTestNow();
        }
    }
}
