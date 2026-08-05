<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\ApiRequest;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: 'Autentikasi diperlukan.',
                    status: 401,
                );
            }

            return redirect()->route('login');
        }

        if (! $user->is_active) {
            if (ApiRequest::shouldReturnJson($request)) {
                $accessToken = $user->currentAccessToken();

                if ($accessToken instanceof PersonalAccessToken) {
                    $accessToken->delete();
                }

                return ApiResponse::error(
                    message: 'Akses tidak tersedia.',
                    status: 403,
                );
            }

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['username' => 'Sesi tidak dapat digunakan. Silakan masuk kembali.']);
        }

        return $next($request);
    }
}
