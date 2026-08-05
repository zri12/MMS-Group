<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiJsonResponseHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_api_without_accept_header_returns_json(): void
    {
        $response = $this->get('/api/v1/auth/profile');

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Autentikasi diperlukan.',
        ]);
        $this->assertApiJson($response);
    }

    public function test_validation_error_without_accept_header_returns_api_envelope(): void
    {
        $response = $this->post('/api/v1/auth/login', []);

        $response->assertUnprocessable();
        $response->assertJson([
            'success' => false,
            'message' => 'Data yang diberikan tidak valid.',
        ]);
        $response->assertJsonValidationErrors(['username', 'password', 'device_name']);
        $this->assertApiJson($response);
    }

    public function test_wrong_role_and_missing_ability_without_accept_header_return_json(): void
    {
        $admin = User::factory()->admin()->create();
        $adminToken = $admin->createToken('mms-marketing:Admin', ['marketing-mobile'])->plainTextToken;

        $this->withToken($adminToken)
            ->get('/api/v1/auth/profile')
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'Akses tidak tersedia.',
            ]);

        $marketing = $this->marketingUser();
        $limitedToken = $marketing->createToken('mms-marketing:Limited', ['limited'])->plainTextToken;

        $response = $this->withToken($limitedToken)->get('/api/v1/auth/profile');

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Akses tidak tersedia.',
        ]);
        $this->assertApiJson($response);
    }

    public function test_inactive_api_without_accept_header_revokes_current_token(): void
    {
        $marketing = $this->marketingUser(active: false);
        $accessToken = $marketing->createToken('mms-marketing:Device', ['marketing-mobile']);

        $response = $this->withToken($accessToken->plainTextToken)->get('/api/v1/auth/profile');

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Akses tidak tersedia.',
        ]);
        $this->assertApiJson($response);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $accessToken->accessToken->id,
        ]);
    }

    public function test_not_found_api_without_accept_header_returns_json(): void
    {
        $response = $this->get('/api/v1/not-registered');

        $response->assertNotFound();
        $response->assertJson([
            'success' => false,
            'message' => 'Data tidak ditemukan.',
        ]);
        $this->assertApiJson($response);
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

    private function assertApiJson($response): void
    {
        $this->assertStringContainsString('application/json', (string) $response->headers->get('content-type'));
        $this->assertStringNotContainsString('<!DOCTYPE html>', $response->getContent());
    }
}
