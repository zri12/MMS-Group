<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CustomerRevisionContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_prospects_and_members_are_displayed_in_separate_admin_lists(): void
    {
        $admin = User::factory()->admin()->create();
        $marketing = $this->marketingProfile();
        $prospect = Prospect::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Prospek Terpisah',
        ]);
        $member = Member::factory()->create([
            'marketing_profile_id' => $marketing->id,
            'name' => 'Anggota Terpisah',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.prospects.index'))
            ->assertOk()
            ->assertSee('Data Prospek')
            ->assertSee($prospect->name)
            ->assertDontSee($member->name);

        $this->actingAs($admin)
            ->get(route('admin.members.index'))
            ->assertOk()
            ->assertSee('Data Anggota')
            ->assertSee($member->name)
            ->assertDontSee($prospect->name);
    }

    public function test_operational_recap_remains_an_admin_only_screen(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.operational-recaps.index'))
            ->assertOk();

        $this->assertFalse(Route::has('api.v1.operational-recaps.index'));
    }

    private function marketingProfile(): MarketingProfile
    {
        $user = User::factory()->marketing()->create();

        return MarketingProfile::factory()->create(['user_id' => $user->id]);
    }
}
