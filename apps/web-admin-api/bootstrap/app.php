<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsMarketing;
use App\Support\ApiRequest;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'active' => EnsureUserIsActive::class,
            'admin' => EnsureUserIsAdmin::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'marketing' => EnsureUserIsMarketing::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request): string => route('login'));
        $middleware->redirectUsersTo(fn (Request $request): string => route('admin.home'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
            'token',
        ]);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => ApiRequest::shouldReturnJson($request),
        );

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: 'Autentikasi diperlukan.',
                    status: 401,
                );
            }

            return null;
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: 'Akses tidak tersedia.',
                    status: 403,
                );
            }

            return null;
        });

        $exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: 'Akses tidak tersedia.',
                    status: 403,
                );
            }

            return null;
        });

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::validationError($exception->errors());
            }

            return null;
        });

        $exceptions->render(function (ThrottleRequestsException $exception, Request $request) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: 'Terlalu banyak percobaan. Coba lagi nanti.',
                    status: 429,
                );
            }

            return null;
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: 'Data tidak ditemukan.',
                    status: 404,
                );
            }

            return null;
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, Request $request) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: 'Metode tidak tersedia.',
                    status: 405,
                );
            }

            return null;
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if (ApiRequest::shouldReturnJson($request)) {
                return ApiResponse::error(
                    message: $exception->getStatusCode() >= 500
                        ? 'Terjadi kesalahan.'
                        : 'Permintaan tidak dapat diproses.',
                    status: $exception->getStatusCode(),
                );
            }

            return null;
        });
    })->create();
