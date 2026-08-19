<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\DayName;
use App\Enums\OperationalAttachmentType;
use App\Enums\ProspectStatus;
use App\Enums\VisitResult;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use App\Models\User;
use App\Models\VisitReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_operational_report_list_and_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $report = DailyOperationalReport::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'resort' => 'Gedebage',
            'report_date' => '2026-07-20',
            'day_name' => DayName::Monday->value,
        ]);
        $report->attachments()->create([
            'type' => OperationalAttachmentType::Disbursement,
            'photo_path' => 'operational-report-attachments/disbursement.jpg',
            'caption' => 'Pencairan anggota',
            'uploaded_at' => now(),
        ]);
        $report->attachments()->create([
            'type' => OperationalAttachmentType::TransferProof,
            'photo_path' => 'operational-report-attachments/transfer.jpg',
            'caption' => 'Transfer setoran',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/operational-reports?search=Gedebage&date_from=2026-07-20&date_to=2026-07-20')
            ->assertOk()
            ->assertSee('Laporan Operasional')
            ->assertSee('Gedebage')
            ->assertSee('M01');

        $this->actingAs($admin)
            ->get(route('admin.operational-reports.show', $report))
            ->assertOk()
            ->assertSee('Detail Laporan Operasional')
            ->assertSee('Target Sebelumnya')
            ->assertSee('Foto Pencairan')
            ->assertSee('Pencairan anggota')
            ->assertSee('Foto Bukti Transfer')
            ->assertSee('Transfer setoran');
    }

    public function test_admin_can_view_visit_report_list_and_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $prospect = Prospect::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Ahmad Hidayat',
        ]);
        $report = VisitReport::factory()->create([
            'prospect_id' => $prospect->id,
            'marketing_profile_id' => $marketing->id,
            'visit_result' => VisitResult::Met->value,
            'prospect_status' => ProspectStatus::Interested->value,
        ]);

        $this->actingAs($admin)
            ->get('/admin/visit-reports?search=Ahmad&result=Berhasil%20Bertemu')
            ->assertOk()
            ->assertSee('Laporan Kunjungan')
            ->assertSee('Ahmad Hidayat')
            ->assertSee('Berhasil Bertemu');

        $this->actingAs($admin)
            ->get(route('admin.visit-reports.show', $report))
            ->assertOk()
            ->assertSee('Detail Laporan Kunjungan')
            ->assertSee('Lokasi');
    }

    public function test_guest_and_marketing_cannot_open_admin_reports(): void
    {
        $marketingUser = User::factory()->marketing()->create();

        $this->get('/admin/operational-reports')->assertRedirect(route('login', absolute: false));
        $this->get('/admin/visit-reports')->assertRedirect(route('login', absolute: false));

        $this->actingAs($marketingUser)
            ->get('/admin/operational-reports')
            ->assertForbidden();
        $this->actingAs($marketingUser)
            ->get('/admin/visit-reports')
            ->assertForbidden();
    }

    private function marketingProfile(): MarketingProfile
    {
        $user = User::factory()->marketing()->create([
            'name' => 'Marketing M01',
            'username' => 'm01.report',
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
