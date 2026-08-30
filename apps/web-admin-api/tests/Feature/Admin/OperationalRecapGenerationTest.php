<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\OperationalRecapRow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalRecapGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        config(['mms.testing.allow_sunday_operations' => false]);

        parent::tearDown();
    }

    public function test_admin_can_generate_an_all_marketing_recap_from_operational_reports(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Rekap']);
        $marketingOne = MarketingProfile::factory()->create(['code' => 'M01']);
        $marketingTwo = MarketingProfile::factory()->create(['code' => 'M02']);

        $previousRecap = OperationalRecap::factory()->create([
            'recap_date' => '2026-07-18',
            'day_name' => DayName::Saturday->value,
            'created_by' => $admin->id,
        ]);
        OperationalRecapRow::factory()->create([
            'operational_recap_id' => $previousRecap->id,
            'marketing_profile_id' => $marketingOne->id,
            'drop_total' => 500_000,
            'storting_total' => 200_000,
            'current_circulation' => 3_000_000,
        ]);

        Member::factory()->create([
            'marketing_profile_id' => $marketingOne->id,
            'approval_status' => MemberApprovalStatus::Approved->value,
            'input_date' => '2026-07-18',
        ]);
        Member::factory()->create([
            'marketing_profile_id' => $marketingOne->id,
            'approval_status' => MemberApprovalStatus::Approved->value,
            'input_date' => '2026-07-20',
        ]);
        Member::factory()->create([
            'marketing_profile_id' => $marketingOne->id,
            'approval_status' => MemberApprovalStatus::Rejected->value,
            'input_date' => '2026-07-20',
        ]);
        DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $marketingOne->id,
            'report_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
            'previous_target_amount' => 1_500_000,
            'incoming_target_amount' => 2_000_000,
            'outgoing_target_amount' => 500_000,
            'total_target_amount' => 3_000_000,
            'drop_amount' => 1_000_000,
            'storting' => 750_000,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.operational-recaps.generate'), ['recap_date' => '2026-07-20'])
            ->assertRedirect();

        $recap = OperationalRecap::query()->whereDate('recap_date', '2026-07-20')->firstOrFail();
        $firstRow = $recap->rows()->where('marketing_profile_id', $marketingOne->id)->firstOrFail();
        $secondRow = $recap->rows()->where('marketing_profile_id', $marketingTwo->id)->firstOrFail();

        $this->assertSame('RKP-20260720', $recap->report_number);
        $this->assertSame('Selesai', $recap->status);
        $this->assertSame(1, $firstRow->members_l);
        $this->assertSame(1, $firstRow->members_m);
        $this->assertSame(0, $firstRow->members_k);
        $this->assertSame(2, $firstRow->members_s);
        $this->assertSame(1_500_000, $firstRow->target_previous);
        $this->assertSame(2_000_000, $firstRow->target_incoming);
        $this->assertSame(500_000, $firstRow->target_outgoing);
        $this->assertSame(3_000_000, $firstRow->target_s);
        $this->assertSame(500_000, $firstRow->drop_previous);
        $this->assertSame(1_000_000, $firstRow->drop_current);
        $this->assertSame(1_500_000, $firstRow->drop_total);
        $this->assertSame(200_000, $firstRow->storting_previous);
        $this->assertSame(750_000, $firstRow->storting_current);
        $this->assertSame(950_000, $firstRow->storting_total);
        $this->assertSame('50.00', $firstRow->percentage);
        $this->assertSame(3_000_000, $firstRow->previous_circulation);
        $this->assertSame(4_500_000, $firstRow->current_circulation);
        $this->assertSame('Admin Rekap', $firstRow->followed_by);
        $this->assertSame(0, $secondRow->target_s);
        $this->assertSame(0, $secondRow->drop_total);

        $this->actingAs($admin)
            ->post(route('admin.operational-recaps.generate'), ['recap_date' => '2026-07-20'])
            ->assertRedirect();

        $this->assertDatabaseCount('operational_recaps', 2);
        $this->assertSame(2, $recap->fresh()->rows()->count());
    }

    public function test_recap_generation_rejects_sunday(): void
    {
        config(['mms.testing.allow_sunday_operations' => false]);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.operational-recaps.index'))
            ->post(route('admin.operational-recaps.generate'), ['recap_date' => '2026-07-19'])
            ->assertRedirect(route('admin.operational-recaps.index'))
            ->assertSessionHasErrors('recap_date');
    }

    public function test_recap_generation_allows_sunday_when_test_mode_is_enabled(): void
    {
        config(['mms.testing.allow_sunday_operations' => true]);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.operational-recaps.generate'), ['recap_date' => '2026-08-30'])
            ->assertRedirect();

        $this->assertDatabaseHas('operational_recaps', [
            'recap_date' => '2026-08-30 00:00:00',
            'day_name' => DayName::Sunday->value,
        ]);
    }
}
