<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\MarketingAuthController;
use App\Http\Controllers\Api\V1\MarketingMemberController;
use App\Http\Controllers\Api\V1\MarketingOperationalReportController;
use App\Http\Controllers\Api\V1\MarketingProspectController;
use App\Http\Controllers\Api\V1\MarketingScheduleController;
use App\Http\Controllers\Api\V1\MarketingTrackingController;
use App\Http\Controllers\Api\V1\MarketingVisitReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Endpoint REST API versi 1 untuk aplikasi marketing Flutter.
| Prefix /api/v1 sudah diterapkan melalui routes/api.php.
|
*/

// Health check - tidak memerlukan autentikasi
Route::get('/health', HealthController::class)
    ->name('api.v1.health');

Route::prefix('auth')
    ->name('api.v1.auth.')
    ->group(function (): void {
        Route::post('/login', [MarketingAuthController::class, 'login'])
            ->name('login');

        Route::middleware([
            'auth:sanctum',
            'active',
            'marketing',
            'abilities:'.config('mms.auth.marketing_api_token_ability', 'marketing-mobile'),
        ])->group(function (): void {
            Route::get('/profile', [MarketingAuthController::class, 'profile'])
                ->name('profile');

            Route::post('/logout', [MarketingAuthController::class, 'logout'])
                ->name('logout');
        });
    });

Route::middleware([
    'auth:sanctum',
    'active',
    'marketing',
    'abilities:'.config('mms.auth.marketing_api_token_ability', 'marketing-mobile'),
])->name('api.v1.')->group(function (): void {
    Route::get('/schedules', [MarketingScheduleController::class, 'index'])
        ->name('schedules.index');
    Route::get('/schedules/today', [MarketingScheduleController::class, 'today'])
        ->name('schedules.today');
    Route::get('/schedules/{schedule}', [MarketingScheduleController::class, 'show'])
        ->name('schedules.show');
    Route::patch('/schedules/{schedule}/status', [MarketingScheduleController::class, 'updateStatus'])
        ->name('schedules.status');

    Route::get('/prospects', [MarketingProspectController::class, 'index'])
        ->name('prospects.index');
    Route::post('/prospects', [MarketingProspectController::class, 'store'])
        ->name('prospects.store');
    Route::get('/prospects/{prospect}', [MarketingProspectController::class, 'show'])
        ->name('prospects.show');
    Route::put('/prospects/{prospect}', [MarketingProspectController::class, 'update'])
        ->name('prospects.update');

    Route::get('/members', [MarketingMemberController::class, 'index'])
        ->name('members.index');
    Route::post('/members', [MarketingMemberController::class, 'store'])
        ->name('members.store');
    Route::get('/members/{member}', [MarketingMemberController::class, 'show'])
        ->name('members.show');

    Route::get('/operational-reports', [MarketingOperationalReportController::class, 'index'])
        ->name('operational-reports.index');
    Route::post('/operational-reports', [MarketingOperationalReportController::class, 'store'])
        ->name('operational-reports.store');
    Route::get('/operational-reports/{operationalReport}', [MarketingOperationalReportController::class, 'show'])
        ->name('operational-reports.show');

    Route::get('/visit-reports', [MarketingVisitReportController::class, 'index'])
        ->name('visit-reports.index');
    Route::post('/visit-reports', [MarketingVisitReportController::class, 'store'])
        ->name('visit-reports.store');
    Route::get('/visit-reports/{visitReport}', [MarketingVisitReportController::class, 'show'])
        ->name('visit-reports.show');

    Route::get('/tracking/sessions', [MarketingTrackingController::class, 'index'])
        ->name('tracking.sessions.index');
    Route::get('/tracking/sessions/current', [MarketingTrackingController::class, 'current'])
        ->name('tracking.sessions.current');
    Route::post('/tracking/sessions/start', [MarketingTrackingController::class, 'start'])
        ->name('tracking.sessions.start');
    Route::get('/tracking/sessions/{trackingSession}', [MarketingTrackingController::class, 'show'])
        ->name('tracking.sessions.show');
    Route::post('/tracking/sessions/{trackingSession}/points', [MarketingTrackingController::class, 'storePoint'])
        ->name('tracking.sessions.points.store');
    Route::post('/tracking/sessions/{trackingSession}/points/batch', [MarketingTrackingController::class, 'storePointBatch'])
        ->name('tracking.sessions.points.batch');
    Route::post('/tracking/sessions/{trackingSession}/stop', [MarketingTrackingController::class, 'stop'])
        ->name('tracking.sessions.stop');
});
