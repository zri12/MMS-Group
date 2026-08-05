<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MarketingLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_marketing_with_profile_can_login(): void
    {
        $marketing = $this->marketingUser();

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $marketing->id,
                    'username' => 'm01.deden',
                    'role' => 'marketing',
                    'marketing' => [
                        'code' => 'M01',
                        'area' => 'Gedebage',
                    ],
                ],
            ],
        ]);
        $response->assertJsonMissingPath('data.user.password');
        $response->assertJsonMissingPath('data.user.remember_token');
        $this->assertNotEmpty($response->json('data.token'));
        $this->assertNotNull($marketing->fresh()->last_login_at);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $marketing->id,
            'name' => 'mms-marketing:Pixel 8',
        ]);
        $this->assertSame(['marketing-mobile'], $marketing->tokens()->first()->abilities);
        $this->assertGuest();
    }

    public function test_username_and_device_name_are_normalized_on_login(): void
    {
        $marketing = $this->marketingUser();

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => '  M01.DEDEN  ',
            'password' => 'CorrectPassword123!',
            'device_name' => "  Pixel   8 \t Device  ",
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $marketing->id,
            'name' => 'mms-marketing:Pixel 8 Device',
        ]);
    }

    public function test_api_password_is_not_trimmed(): void
    {
        $this->marketingUser(password: '  Correct Password  ');

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'Correct Password',
            'device_name' => 'Pixel 8',
        ])->assertUnauthorized();

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => '  Correct Password  ',
            'device_name' => 'Pixel 8',
        ])->assertOk();
    }

    public function test_device_name_is_required(): void
    {
        $this->marketingUser();

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
        ]);

        $response->assertUnprocessable();
        $response->assertJson([
            'success' => false,
            'message' => 'Data yang diberikan tidak valid.',
        ]);
        $response->assertJsonValidationErrors('device_name');
    }

    public function test_required_fields_and_device_name_constraints_are_validated(): void
    {
        $this->postJson('/api/v1/auth/login', [])->assertJsonValidationErrors([
            'username',
            'password',
            'device_name',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
            'device_name' => '    ',
        ])->assertJsonValidationErrors('device_name');

        $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
            'device_name' => str_repeat('a', 101),
        ])->assertJsonValidationErrors('device_name');
    }

    public function test_non_scalar_username_and_device_name_are_validation_errors(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => ['m01.deden'],
            'password' => 'CorrectPassword123!',
            'device_name' => ['Pixel 8'],
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['username', 'device_name']);
    }

    public function test_admin_cannot_login_to_marketing_api(): void
    {
        User::factory()->admin()->create([
            'username' => 'admin.mms',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'admin.mms',
            'password' => 'CorrectPassword123!',
            'device_name' => 'Admin Device',
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Username atau password tidak sesuai.',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_inactive_marketing_cannot_login_to_marketing_api(): void
    {
        $this->marketingUser(active: false);

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Username atau password tidak sesuai.',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_marketing_without_profile_cannot_login_to_marketing_api(): void
    {
        User::factory()->marketing()->create([
            'username' => 'm01.deden',
            'password' => Hash::make('CorrectPassword123!'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Username atau password tidak sesuai.',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_wrong_password_uses_generic_failure(): void
    {
        $this->marketingUser();

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'WrongPassword123!',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Username atau password tidak sesuai.',
        ]);
    }

    public function test_missing_username_uses_generic_failure(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'missing.user',
            'password' => 'CorrectPassword123!',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Username atau password tidak sesuai.',
        ]);
    }

    public function test_plain_token_is_different_from_stored_hash_and_multiple_devices_are_isolated(): void
    {
        $marketing = $this->marketingUser();

        $first = $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
            'device_name' => 'Pixel 8',
        ])->assertOk();

        $second = $this->postJson('/api/v1/auth/login', [
            'username' => 'm01.deden',
            'password' => 'CorrectPassword123!',
            'device_name' => 'Tablet',
        ])->assertOk();

        $this->assertNotSame($first->json('data.token'), $second->json('data.token'));
        $this->assertDatabaseCount('personal_access_tokens', 2);
        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'mms-marketing:Pixel 8']);
        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'mms-marketing:Tablet']);
        $this->assertNotSame(
            $first->json('data.token'),
            DB::table('personal_access_tokens')->where('tokenable_id', $marketing->id)->orderBy('id')->value('token')
        );
        $this->assertSame(['marketing-mobile'], $marketing->tokens()->oldest('id')->first()->abilities);
        $this->assertNotContains('*', $marketing->tokens()->oldest('id')->first()->abilities);
    }

    private function marketingUser(bool $active = true, string $password = 'CorrectPassword123!'): User
    {
        $user = User::factory()->marketing()->create([
            'name' => 'Deden',
            'username' => 'm01.deden',
            'password' => Hash::make($password),
            'is_active' => $active,
        ]);

        MarketingProfile::factory()->create([
            'user_id' => $user->id,
            'code' => 'M01',
            'area' => 'Gedebage',
            'phone' => '081200000001',
        ]);

        return $user;
    }
}
