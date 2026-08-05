<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Support\ApiRequest;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsMarketing
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

            abort(401, 'Autentikasi diperlukan.');
        }

        if ($user->role !== UserRole::Marketing || $user->marketingProfile === null) {
            return ApiResponse::error(
                message: 'Akses tidak tersedia.',
                status: 403,
            );
        }

        return $next($request);
    }
}
