<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MarketingLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_deletes_only_current_token(): void
    {
        $marketing = $this->marketingUser();
        $currentToken = $marketing->createToken('mms-marketing:Pixel 8', ['marketing-mobile']);
        $otherToken = $marketing->createToken('mms-marketing:Tablet', ['marketing-mobile']);

        $response = $this->withToken($currentToken->plainTextToken)->postJson('/api/v1/auth/logout');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $currentToken->accessToken->id,
        ]);
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $otherToken->accessToken->id,
        ]);
        app('auth')->forgetGuards();
        $this->withToken($otherToken->plainTextToken)->getJson('/api/v1/auth/profile')->assertOk();
    }

    public function test_logged_out_token_can_no_longer_access_profile(): void
    {
        $marketing = $this->marketingUser();
        $token = $marketing->createToken('mms-marketing:Pixel 8', ['marketing-mobile'])->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();
        app('auth')->forgetGuards();

        $response = $this->withToken($token)->getJson('/api/v1/auth/profile');

        $response->assertUnauthorized();
    }

    public function test_logout_requires_marketing_token_and_post_method(): void
    {
        $admin = User::factory()->admin()->create();
        $adminToken = $admin->createToken('mms-marketing:Admin', ['marketing-mobile'])->plainTextToken;

        $this->postJson('/api/v1/auth/logout')->assertUnauthorized();
        $this->withToken($adminToken)->postJson('/api/v1/auth/logout')->assertForbidden();

        $marketing = $this->marketingUser();
        $token = $marketing->createToken('mms-marketing:Pixel 8', ['marketing-mobile'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/auth/logout')->assertStatus(405);
    }

    public function test_logout_response_does_not_return_token(): void
    {
        $marketing = $this->marketingUser();
        $token = $marketing->createToken('mms-marketing:Pixel 8', ['marketing-mobile'])->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/auth/logout');

        $response->assertOk();
        $response->assertJsonMissingPath('data.token');
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
