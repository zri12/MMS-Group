<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Support\ApiRequest;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
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

            if (app('router')->has('login')) {
                return redirect()->route('login');
            }

            abort(401, 'Autentikasi diperlukan.');
        }

        if ($user->role !== UserRole::Admin) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: 'Akses tidak tersedia.',
                    status: 403,
                );
            }

            abort(403, 'Akses tidak tersedia.');
        }

        return $next($request);
    }
}
