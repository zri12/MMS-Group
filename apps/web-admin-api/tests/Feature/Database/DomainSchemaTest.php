<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Enums\DayName;
use App\Models\MarketingProfile;
use App\Models\MarketingWorkDay;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\OperationalRecapRow;
use App\Models\Prospect;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DomainSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_domain_tables_are_available(): void
    {
        foreach ($this->domainTables() as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table [{$table}] is missing.");
        }
    }

    public function test_domain_tables_have_critical_columns(): void
    {
        foreach ($this->criticalColumns() as $table => $columns) {
            $this->assertTrue(Schema::hasColumns($table, $columns), "Table [{$table}] is missing critical columns.");
        }
    }

    public function test_no_unplanned_domain_tables_exist(): void
    {
        foreach (['roles', 'permissions', 'media', 'activity_logs', 'notifications', 'audit_logs', 'attachments', 'settings', 'branches', 'devices'] as $table) {
            $this->assertFalse(Schema::hasTable($table), "Unplanned table [{$table}] exists.");
        }
    }

    public function test_testing_database_is_sqlite_in_memory(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }

    public function test_prospect_member_relation_uses_member_source_prospect_only(): void
    {
        $this->assertFalse(Schema::hasColumn('prospects', 'linked_member_id'));
        $this->assertTrue(Schema::hasColumn('members', 'source_prospect_id'));
    }

    public function test_source_prospect_id_is_unique_nullable_and_null_on_delete(): void
    {
        $prospect = Prospect::factory()->create();

        $member = Member::factory()->create(['source_prospect_id' => $prospect->id]);
        Member::factory()->count(2)->create(['source_prospect_id' => null]);

        $this->assertDatabaseConstraintViolation(fn () => Member::factory()->create(['source_prospect_id' => $prospect->id]));

        $prospect->forceDelete();
        $this->assertNull($member->fresh()->source_prospect_id);
    }

    public function test_core_unique_constraints_are_enforced(): void
    {
        User::factory()->create(['username' => 'unique-user']);
        $this->assertDatabaseConstraintViolation(fn () => User::factory()->create(['username' => 'unique-user']));

        MarketingProfile::factory()->create(['code' => 'M50']);
        $this->assertDatabaseConstraintViolation(fn () => MarketingProfile::factory()->create(['code' => 'M50']));

        Member::factory()->create(['member_number' => 'MBR-UNIQUE']);
        $this->assertDatabaseConstraintViolation(fn () => Member::factory()->create(['member_number' => 'MBR-UNIQUE']));

        Member::factory()->create(['loan_number' => 'LN-UNIQUE']);
        $this->assertDatabaseConstraintViolation(fn () => Member::factory()->create(['loan_number' => 'LN-UNIQUE']));

        Prospect::factory()->create(['local_uuid' => '11111111-1111-4111-8111-111111111111']);
        $this->assertDatabaseConstraintViolation(fn () => Prospect::factory()->create(['local_uuid' => '11111111-1111-4111-8111-111111111111']));

        $marketingProfile = MarketingProfile::factory()->create();
        MarketingWorkDay::factory()->create([
            'marketing_profile_id' => $marketingProfile->id,
            'day_name' => DayName::Monday,
        ]);
        $this->assertDatabaseConstraintViolation(fn () => MarketingWorkDay::factory()->create([
            'marketing_profile_id' => $marketingProfile->id,
            'day_name' => DayName::Monday,
        ]));
    }

    public function test_tracking_points_cascade_when_session_is_deleted(): void
    {
        $trackingSession = TrackingSession::factory()
            ->has(TrackingPoint::factory()->count(2), 'points')
            ->create();

        $trackingSession->delete();

        $this->assertSame(0, TrackingPoint::query()->count());
    }

    public function test_operational_recap_rows_cascade_when_recap_is_deleted(): void
    {
        $operationalRecap = OperationalRecap::factory()
            ->has(OperationalRecapRow::factory()->count(2), 'rows')
            ->create();

        $operationalRecap->delete();

        $this->assertSame(0, OperationalRecapRow::query()->count());
    }

    public function test_tracking_point_created_at_is_required_and_defaults_to_current_timestamp(): void
    {
        $trackingSession = TrackingSession::factory()->create();

        DB::table('tracking_points')->insert([
            'local_uuid' => '22222222-2222-4222-8222-222222222222',
            'tracking_session_id' => $trackingSession->id,
            'latitude' => -6.2000000,
            'longitude' => 106.8000000,
            'point_type' => 'Perjalanan',
            'recorded_at' => '2026-07-20 08:06:00',
            'received_at' => '2026-07-20 08:06:05',
        ]);

        $trackingPoint = TrackingPoint::query()
            ->where('local_uuid', '22222222-2222-4222-8222-222222222222')
            ->firstOrFail();

        $this->assertNotNull($trackingPoint->created_at);

        $this->assertDatabaseConstraintViolation(fn () => DB::table('tracking_points')->insert([
            'local_uuid' => '33333333-3333-4333-8333-333333333333',
            'tracking_session_id' => $trackingSession->id,
            'latitude' => -6.2000000,
            'longitude' => 106.8000000,
            'point_type' => 'Perjalanan',
            'recorded_at' => '2026-07-20 08:07:00',
            'received_at' => '2026-07-20 08:07:05',
            'created_at' => null,
        ]));
    }

    public function test_historical_reports_remain_when_user_is_inactivated(): void
    {
        $member = Member::factory()->create();

        $member->marketingProfile->user->update(['is_active' => false]);

        $this->assertDatabaseHas('members', ['id' => $member->id]);
        $this->assertDatabaseHas('marketing_profiles', ['id' => $member->marketing_profile_id]);
    }

    /**
     * @return list<string>
     */
    private function domainTables(): array
    {
        return [
            'users',
            'marketing_profiles',
            'marketing_work_days',
            'prospects',
            'members',
            'marketing_schedules',
            'daily_operational_reports',
            'visit_reports',
            'tracking_sessions',
            'tracking_points',
            'operational_recaps',
            'operational_recap_rows',
            'personal_access_tokens',
            'cache',
            'jobs',
            'sessions',
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    private function criticalColumns(): array
    {
        return [
            'users' => ['id', 'name', 'username', 'email', 'password', 'role', 'is_active', 'last_login_at', 'deleted_at'],
            'marketing_profiles' => ['id', 'user_id', 'code', 'phone', 'area', 'profile_photo_path'],
            'marketing_work_days' => ['id', 'marketing_profile_id', 'day_name'],
            'prospects' => ['id', 'local_uuid', 'marketing_profile_id', 'name', 'phone', 'status', 'sync_status', 'deleted_at'],
            'members' => ['id', 'local_uuid', 'marketing_profile_id', 'source_prospect_id', 'member_number', 'loan_number', 'approval_status', 'member_photo_path', 'approved_by', 'rejected_by', 'sync_status', 'deleted_at'],
            'marketing_schedules' => ['id', 'marketing_profile_id', 'prospect_id', 'schedule_date', 'start_time', 'status', 'created_by', 'deleted_at'],
            'daily_operational_reports' => ['id', 'local_uuid', 'marketing_profile_id', 'report_date', 'storting', 'drop_amount', 'total_target_amount', 'sync_status'],
            'visit_reports' => ['id', 'local_uuid', 'prospect_id', 'marketing_profile_id', 'visit_date', 'visit_result', 'prospect_status', 'photo_path', 'sync_status'],
            'tracking_sessions' => ['id', 'local_uuid', 'marketing_profile_id', 'schedule_id', 'session_date', 'started_at', 'ended_at', 'status', 'distance_meters', 'visit_count'],
            'tracking_points' => ['id', 'local_uuid', 'tracking_session_id', 'latitude', 'longitude', 'accuracy_meters', 'recorded_at', 'received_at'],
            'operational_recaps' => ['id', 'report_number', 'recap_date', 'day_name', 'status', 'created_by'],
            'operational_recap_rows' => ['id', 'operational_recap_id', 'marketing_profile_id', 'mg', 'members_l', 'target_s', 'percentage', 'followed_by', 'morning_cash'],
        ];
    }

    private function assertDatabaseConstraintViolation(callable $callback): void
    {
        try {
            $callback();
        } catch (QueryException $exception) {
            $this->assertNotEmpty($exception->getMessage());

            return;
        }

        $this->fail('Expected a database unique constraint violation.');
    }
}
