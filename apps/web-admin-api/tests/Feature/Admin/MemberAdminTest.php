<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_member_list_and_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $member = Member::factory()->pending()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Siti Aminah',
            'member_number' => 'AGT-001',
            'input_date' => '2026-07-20',
        ]);

        $this->actingAs($admin)
            ->get('/admin/members?search=Siti&day=Senin')
            ->assertOk()
            ->assertSee('Data Anggota')
            ->assertSee('Siti Aminah')
            ->assertSee('AGT-001');

        $this->actingAs($admin)
            ->get(route('admin.members.show', $member))
            ->assertOk()
            ->assertSee('Persetujuan')
            ->assertSee('Siti Aminah');
    }

    public function test_admin_can_approve_pending_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = Member::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.members.approval', $member), [
                'approval_status' => MemberApprovalStatus::Approved->value,
            ])
            ->assertRedirect();

        $member->refresh();

        $this->assertSame(MemberApprovalStatus::Approved, $member->approval_status);
        $this->assertSame($admin->id, $member->approved_by);
        $this->assertNotNull($member->approved_at);
        $this->assertNull($member->rejected_by);
        $this->assertNull($member->rejection_reason);
    }

    public function test_admin_can_reject_pending_member(): void
    {
        $admin = User::factory()->admin()->create();
        $member = Member::factory()->pending()->create();

        $this->actingAs($admin)
            ->patch(route('admin.members.approval', $member), [
                'approval_status' => MemberApprovalStatus::Rejected->value,
                'rejection_reason' => 'Alamat belum lengkap.',
            ])
            ->assertRedirect();

        $member->refresh();

        $this->assertSame(MemberApprovalStatus::Rejected, $member->approval_status);
        $this->assertSame($admin->id, $member->rejected_by);
        $this->assertNotNull($member->rejected_at);
        $this->assertNull($member->approved_by);
        $this->assertSame('Alamat belum lengkap.', $member->rejection_reason);
    }

    public function test_admin_cannot_change_final_member_status(): void
    {
        $admin = User::factory()->admin()->create();
        $member = Member::factory()->approved()->create();

        $this->actingAs($admin)
            ->from(route('admin.members.show', $member))
            ->patch(route('admin.members.approval', $member), [
                'approval_status' => MemberApprovalStatus::Rejected->value,
                'rejection_reason' => 'Tidak valid.',
            ])
            ->assertRedirect(route('admin.members.show', $member, absolute: false))
            ->assertSessionHasErrors('approval_status');

        $this->assertSame(MemberApprovalStatus::Approved, $member->refresh()->approval_status);
    }

    public function test_guest_and_marketing_cannot_open_admin_members(): void
    {
        $marketingUser = User::factory()->marketing()->create();

        $this->get('/admin/members')->assertRedirect(route('login', absolute: false));

        $this->actingAs($marketingUser)
            ->get('/admin/members')
            ->assertForbidden();
    }

    private function marketingProfile(): MarketingProfile
    {
        $user = User::factory()->marketing()->create([
            'name' => 'Marketing M01',
            'username' => 'm01.member',
        ]);

        $profile = MarketingProfile::factory()->create([
            'user_id' => $user->id,
            'code' => 'M01',
            'area' => 'Gedebage',
        ]);

        $profile->workDays()->create(['day_name' => DayName::Monday->value]);

        return $profile;
    }
}
