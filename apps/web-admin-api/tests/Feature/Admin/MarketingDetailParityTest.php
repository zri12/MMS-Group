<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\Member;
use App\Models\Prospect;
use App\Models\TrackingSession;
use App\Models\User;
use App\Models\VisitReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingDetailParityTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_detail_renders_reference_tabs_and_database_sections(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = MarketingProfile::factory()->create(['code' => 'M01', 'area' => 'Gedebage']);
        $prospect = Prospect::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Siti Aminah',
        ]);

        Member::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Budi Santoso',
        ]);
        DailyOperationalReport::factory()->create(['marketing_profile_id' => $marketing->id]);
        VisitReport::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'prospect_id' => $prospect->id,
        ]);
        MarketingSchedule::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'prospect_id' => $prospect->id,
            'agenda' => 'Follow up calon anggota',
            'created_by' => $admin->id,
        ]);
        TrackingSession::factory()->create(['marketing_profile_id' => $marketing->id]);

        $this->actingAs($admin)
            ->get(route('admin.marketing.show', $marketing, absolute: false))
            ->assertOk()
            ->assertSee('Ringkasan')
            ->assertSee('Jadwal')
            ->assertSee('Tracking')
            ->assertSee('Prospek')
            ->assertSee('Anggota')
            ->assertSee('Laporan Operasional')
            ->assertSee('Kunjungan')
            ->assertSee('Riwayat')
            ->assertSee('Siti Aminah')
            ->assertSee('Budi Santoso')
            ->assertSee('Follow up calon anggota');
    }
}
