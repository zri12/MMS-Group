<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use App\Enums\UserRole;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class ResetDevelopmentUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_removes_login_accounts_and_tokens_without_removing_historical_profiles(): void
    {
        $oldAdmin = User::factory()->admin()->create();
        $pdlUser = User::factory()->marketing()->create();
        $profile = MarketingProfile::factory()->create(['user_id' => $pdlUser->id]);
        MarketingSchedule::factory()->create([
            'marketing_profile_id' => $profile->id,
            'created_by' => $oldAdmin->id,
        ]);
        $pdlUser->createToken('old-token');

        $this->artisan('mms:reset-dev-users', ['--force-local' => true])
            ->assertSuccessful();

        $this->assertSame(1, User::query()->count());
        $this->assertSame(1, User::query()->where('role', UserRole::Admin)->count());
        $this->assertSame(0, User::query()->where('role', UserRole::Marketing)->count());
        $this->assertSame(0, PersonalAccessToken::query()->count());
        $this->assertNull($profile->fresh()->user_id);
        $this->assertDatabaseCount('marketing_schedules', 1);
        $this->assertDatabaseMissing('users', ['username' => $pdlUser->username]);
    }

    public function test_reset_requires_explicit_local_confirmation(): void
    {
        $this->artisan('mms:reset-dev-users')
            ->assertFailed();
    }
}
