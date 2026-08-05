<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MarketingProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/auth/profile');

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Autentikasi diperlukan.',
        ]);
    }

    public function test_marketing_can_read_profile_with_valid_token_and_ability(): void
    {
        $marketing = $this->marketingUser();
        $token = $marketing->createToken('mms-marketing:Pixel 8', ['marketing-mobile'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/auth/profile');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Profil berhasil dimuat.',
            'data' => [
                'user' => [
                    'username' => 'm01.deden',
                    'role' => 'marketing',
                    'marketing' => [
                        'code' => 'M01',
                    ],
                ],
            ],
        ]);
        $response->assertJsonMissingPath('data.user.password');
    }

    public function test_inactive_marketing_token_is_blocked(): void
    {
        $marketing = $this->marketingUser(active: false);
        $accessToken = $marketing->createToken('mms-marketing:Pixel 8', ['marketing-mobile']);
        $token = $accessToken->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/auth/profile');

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Akses tidak tersedia.',
        ]);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $accessToken->accessToken->id,
        ]);

        app('auth')->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/auth/profile')->assertUnauthorized();
    }

    public function test_inactive_current_token_revocation_keeps_other_tokens(): void
    {
        $marketing = $this->marketingUser(active: true);
        $currentToken = $marketing->createToken('mms-marketing:Pixel 8', ['marketing-mobile']);
        $otherToken = $marketing->createToken('mms-marketing:Tablet', ['marketing-mobile']);

        $marketing->forceFill(['is_active' => false])->save();

        $this->withToken($currentToken->plainTextToken)->getJson('/api/v1/auth/profile')
            ->assertForbidden()
            ->assertJsonMissingPath('errors.reason');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $currentToken->accessToken->id,
        ]);
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $otherToken->accessToken->id,
        ]);
    }

    public function test_admin_token_is_blocked_from_marketing_profile_endpoint(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('mms-marketing:Admin', ['marketing-mobile'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/auth/profile');

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Akses tidak tersedia.',
        ]);
    }

    public function test_token_without_marketing_mobile_ability_is_blocked(): void
    {
        $marketing = $this->marketingUser();
        $token = $marketing->createToken('mms-marketing:Pixel 8', ['other-ability'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/auth/profile');

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Akses tidak tersedia.',
        ]);
    }

    public function test_marketing_without_profile_token_is_blocked(): void
    {
        $marketing = User::factory()->marketing()->create([
            'username' => 'm02.no.profile',
        ]);
        $token = $marketing->createToken('mms-marketing:Pixel 8', ['marketing-mobile'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/auth/profile');

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Akses tidak tersedia.',
        ]);
    }

    private function marketingUser(bool $active = true): User
    {
        $user = User::factory()->marketing()->create([
            'name' => 'Deden',
            'username' => 'm01.deden',
            'password' => Hash::make('CorrectPassword123!'),
            'is_active' => $active,
        ]);

        MarketingProfile::factory()->create([
            'user_id' => $user->id,
            'code' => 'M01',
            'area' => 'Gedebage',
        ]);

        return $user;
    }
}
