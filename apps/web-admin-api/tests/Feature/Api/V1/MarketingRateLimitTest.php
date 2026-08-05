<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class MarketingRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_rate_limit_uses_username_and_ip_only(): void
    {
        config([
            'mms.auth.marketing_api_login_max_attempts' => 2,
            'mms.auth.marketing_api_login_decay_seconds' => 30,
        ]);

        RateLimiter::clear('m01.deden|127.0.0.1');
        $this->marketingUser();

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'WrongPassword123!',
            'device_name' => 'Pixel 8',
        ])->assertUnauthorized();

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'WrongPassword123!',
            'device_name' => 'Tablet',
        ])->assertUnauthorized();

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'WrongPassword123!',
            'device_name' => 'Different Device',
        ]);

        $response->assertStatus(429);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_api_login_rate_limit_uses_normalized_username(): void
    {
        config([
            'mms.auth.marketing_api_login_max_attempts' => 1,
            'mms.auth.marketing_api_login_decay_seconds' => 30,
        ]);

        RateLimiter::clear('m01.deden|127.0.0.1');
        $this->marketingUser();

        $this->postJson('/api/v1/auth/login', [
            'username' => '  M01.DEDEN  ',
            'password' => 'WrongPassword123!',
            'device_name' => 'Pixel 8',
        ])->assertUnauthorized();

        $this->assertTrue(RateLimiter::tooManyAttempts('m01.deden|127.0.0.1', 1));
        $this->assertFalse(RateLimiter::tooManyAttempts('  M01.DEDEN  |127.0.0.1', 1));
    }

    public function test_api_login_rate_limit_keeps_device_name_and_password_out_of_key(): void
    {
        config([
            'mms.auth.marketing_api_login_max_attempts' => 2,
            'mms.auth.marketing_api_login_decay_seconds' => 30,
        ]);

        RateLimiter::clear('m01.deden|127.0.0.1');
        $this->marketingUser();

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'Wrong One',
            'device_name' => 'Pixel 8',
        ])->assertUnauthorized();

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'Wrong Two',
            'device_name' => 'Tablet',
        ])->assertUnauthorized();

        $this->assertTrue(RateLimiter::tooManyAttempts('m01.deden|127.0.0.1', 2));
        $this->assertFalse(RateLimiter::tooManyAttempts('m01.deden|Pixel 8|127.0.0.1', 2));
        $this->assertFalse(RateLimiter::tooManyAttempts('m01.deden|Wrong One|127.0.0.1', 2));
    }

    public function test_successful_api_login_clears_rate_limiter(): void
    {
        config([
            'mms.auth.marketing_api_login_max_attempts' => 2,
            'mms.auth.marketing_api_login_decay_seconds' => 30,
        ]);

        RateLimiter::clear('m01.deden|127.0.0.1');
        $this->marketingUser();

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'WrongPassword123!',
            'device_name' => 'Pixel 8',
        ])->assertUnauthorized();

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
            'device_name' => 'Pixel 8',
        ])->assertOk();

        $this->assertFalse(RateLimiter::tooManyAttempts('m01.deden|127.0.0.1', 2));
    }

    private function marketingUser(): User
    {
        $user = User::factory()->marketing()->create([
            'username' => 'm01.deden',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        MarketingProfile::factory()->create([
            'user_id' => $user->id,
            'code' => 'M01',
            'area' => 'Gedebage',
        ]);

        return $user;
    }
}
