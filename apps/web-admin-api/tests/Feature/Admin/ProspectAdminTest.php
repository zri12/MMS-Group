<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\DayName;
use App\Enums\ProspectStatus;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use App\Models\User;
use App\Models\VisitReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProspectAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_prospect_list_and_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $prospect = Prospect::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Ahmad Hidayat',
            'input_date' => '2026-07-20',
            'status' => ProspectStatus::Interested->value,
        ]);
        VisitReport::factory()->create([
            'prospect_id' => $prospect->id,
            'marketing_profile_id' => $marketing->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/prospects?search=Ahmad&day=Senin')
            ->assertOk()
            ->assertSee('Ahmad Hidayat')
            ->assertSee('Data Prospek');

        $this->actingAs($admin)
            ->get(route('admin.prospects.show', $prospect))
            ->assertOk()
            ->assertSee('Riwayat Kunjungan')
            ->assertSee('Ahmad Hidayat');
    }

    public function test_guest_and_marketing_cannot_open_admin_prospects(): void
    {
        $marketingUser = User::factory()->marketing()->create();

        $this->get('/admin/prospects')->assertRedirect(route('login', absolute: false));

        $this->actingAs($marketingUser)
            ->get('/admin/prospects')
            ->assertForbidden();
    }

    private function marketingProfile(): MarketingProfile
    {
        $user = User::factory()->marketing()->create([
            'name' => 'Marketing M01',
            'username' => 'm01.marketing',
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
