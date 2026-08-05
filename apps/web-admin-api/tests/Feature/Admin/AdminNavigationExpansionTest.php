<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\DayName;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
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

class AdminNavigationExpansionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_new_daily_schedule_recap_and_journey_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = MarketingProfile::factory()->create();
        $date = '2026-07-20';

        Prospect::factory()->create(['marketing_profile_id' => $marketing->id, 'input_date' => $date]);
        Member::factory()->create(['marketing_profile_id' => $marketing->id, 'input_date' => $date]);
        DailyOperationalReport::factory()->create(['marketing_profile_id' => $marketing->id, 'report_date' => $date]);
        $prospect = Prospect::factory()->create(['marketing_profile_id' => $marketing->id]);
        VisitReport::factory()->create(['marketing_profile_id' => $marketing->id, 'prospect_id' => $prospect->id, 'visit_date' => $date]);
        MarketingSchedule::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'schedule_date' => $date,
            'agenda' => 'Kunjungan calon anggota',
            'created_by' => $admin->id,
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
        ]);

        $session = TrackingSession::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'session_date' => $date,
            'day_name' => DayName::Monday->value,
        ]);
        TrackingPoint::factory()->create(['tracking_session_id' => $session->id]);

        $this->actingAs($admin)->get('/admin/daily?date=2026-07-20&day=Senin')
            ->assertOk()
            ->assertSee('Rencana Kerja')
            ->assertSee('Laporan Operasional');

        $this->actingAs($admin)->get('/admin/schedules')
            ->assertOk()
            ->assertSee('Jadwal Marketing')
            ->assertSee('Kunjungan calon anggota');

        $this->actingAs($admin)->get('/admin/operational-recaps/'.$recap->id)
            ->assertOk()
            ->assertSee('Rekap Operasional')
            ->assertSee('Sirkulasi')
            ->assertSee('M01');

        $this->actingAs($admin)->get('/admin/journeys')
            ->assertOk()
            ->assertSee('Riwayat Perjalanan')
            ->assertSee($marketing->code);
    }

    public function test_admin_can_create_update_and_delete_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = MarketingProfile::factory()->create(['area' => 'Gedebage']);

        $payload = [
            'marketing_profile_id' => $marketing->id,
            'prospect_id' => null,
            'day_name' => 'Senin',
            'schedule_date' => '2026-07-20',
            'start_time' => '08:30',
            'end_time' => '09:30',
            'consumer_name_snapshot' => 'Ahmad Hidayat',
            'agenda' => 'Presentasi produk',
            'area' => 'Gedebage',
            'resort' => 'Gedebage',
            'destination' => 'Gedebage, Kota Bandung',
            'note' => 'Kunjungan awal',
            'status' => 'Belum Dikunjungi',
        ];

        $this->actingAs($admin)->post('/admin/schedules', $payload)
            ->assertRedirect(route('admin.schedules.index', absolute: false));

        $schedule = MarketingSchedule::query()->firstOrFail();
        $this->assertSame('Presentasi produk', $schedule->agenda);

        $this->actingAs($admin)->put('/admin/schedules/'.$schedule->id, array_merge($payload, [
            'agenda' => 'Follow up produk',
            'status' => 'Berlangsung',
        ]))->assertRedirect(route('admin.schedules.index', absolute: false));

        $this->assertDatabaseHas('marketing_schedules', [
            'id' => $schedule->id,
            'agenda' => 'Follow up produk',
            'status' => 'Berlangsung',
        ]);

        $this->actingAs($admin)->delete('/admin/schedules/'.$schedule->id)
            ->assertRedirect(route('admin.schedules.index', absolute: false));

        $this->assertSoftDeleted('marketing_schedules', ['id' => $schedule->id]);
    }
}
