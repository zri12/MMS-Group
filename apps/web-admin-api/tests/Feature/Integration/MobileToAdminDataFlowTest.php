<?php

declare(strict_types=1);

namespace Tests\Feature\Integration;

use App\Enums\ProspectStatus;
use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class MobileToAdminDataFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_submissions_are_visible_in_the_admin_web(): void
    {
        $admin = User::factory()->admin()->create();
        $marketingUser = User::factory()->marketing()->create([
            'name' => 'Nadia PDL',
            'username' => 'nadia.pdl',
            'password' => Hash::make('password-pdl'),
        ]);
        MarketingProfile::factory()->create([
            'user_id' => $marketingUser->id,
            'code' => 'M01',
            'area' => 'Gedebage',
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'username' => 'nadia.pdl',
            'password' => 'password-pdl',
            'device_name' => 'Android PDL',
        ])->assertOk();
        $token = $login->json('data.token');

        $session = $this->withToken($token)->postJson('/api/v1/tracking/sessions/start', [
            'local_uuid' => (string) Str::uuid(),
            'started_at' => '2026-07-20T08:00:00+07:00',
        ])->assertCreated();

        $this->withToken($token)->postJson('/api/v1/tracking/sessions/'.$session->json('data.session_id').'/points/batch', [
            'points' => [[
                'local_uuid' => (string) Str::uuid(),
                'latitude' => -6.9388,
                'longitude' => 107.7079,
                'accuracy_meters' => 8.5,
                'point_type' => 'Perjalanan',
                'recorded_at' => '2026-07-20T08:05:00+07:00',
            ]],
        ])->assertCreated()->assertJsonPath('data.created_count', 1);

        $this->withToken($token)->postJson('/api/v1/prospects', [
            'local_uuid' => (string) Str::uuid(),
            'name' => 'Calon Anggota Baru',
            'phone' => '081234567890',
            'address' => 'Jl. Gedebage No. 1',
            'business' => 'Toko Harapan',
            'status' => ProspectStatus::New->value,
            'initial_visit_result' => 'Bersedia dihubungi kembali.',
            'resort' => 'Gedebage',
            'input_date' => '2026-07-20',
            'input_time' => '08:10:00',
            'latitude' => -6.9388,
            'longitude' => 107.7079,
            'location_address' => 'Gedebage, Kota Bandung',
        ])->assertCreated();

        $today = now(config('mms.timezone'))->toDateString();

        $this->actingAs($admin)
            ->get('/admin/tracking?date='.$today)
            ->assertOk()
            ->assertSee('M01 - Nadia PDL')
            ->assertSee('data-markers="[{&quot;lat&quot;:-6.9388', false);

        $this->actingAs($admin)
            ->get('/admin/prospects?search=Calon%20Anggota%20Baru')
            ->assertOk()
            ->assertSee('Calon Anggota Baru')
            ->assertSee('Nadia PDL');

    }
}
