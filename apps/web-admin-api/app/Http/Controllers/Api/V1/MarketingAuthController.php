<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\MarketingLoginRequest;
use App\Http\Resources\Api\V1\AuthenticatedMarketingResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketingAuthController
{
    public function login(MarketingLoginRequest $request): JsonResponse
    {
        $user = $request->authenticateMarketing();

        $user->forceFill([
            'last_login_at' => now(),
        ])->save();

        $ability = (string) config('mms.auth.marketing_api_token_ability', 'marketing-mobile');
        $token = $user->createToken(
            name: $this->tokenName($request->validated('device_name')),
            abilities: [$ability],
        )->plainTextToken;

        return ApiResponse::success(
            message: 'Login berhasil.',
            data: [
                'token_type' => 'Bearer',
                'token' => $token,
                'user' => (new AuthenticatedMarketingResource($user->fresh()->load('marketingProfile.workDays')))->resolve(),
            ],
        );
    }

    public function profile(Request $request): JsonResponse
    {
        return ApiResponse::success(
            message: 'Profil berhasil dimuat.',
            data: [
                'user' => (new AuthenticatedMarketingResource(
                    $request->user()->loadMissing('marketingProfile.workDays')
                ))->resolve(),
            ],
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return ApiResponse::success(
            message: 'Logout berhasil.',
            data: null,
        );
    }

    private function tokenName(string $deviceName): string
    {
        $prefix = (string) config('mms.auth.marketing_api_token_prefix', 'mms-marketing');
        $device = Str::of($deviceName)->squish()->limit(100, '')->toString();

        return $prefix.':'.$device;
    }
}
