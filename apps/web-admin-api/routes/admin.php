<?php

use App\Http\Controllers\Admin\DailyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JourneyController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\OperationalRecapController;
use App\Http\Controllers\Admin\OperationalReportController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProspectController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\TrackingController;
use App\Http\Controllers\Admin\VisitReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
|
| Route untuk halaman Web Admin monitoring.
| Semua route admin menggunakan prefix /admin dan name prefix admin.
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'active', 'admin'])
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('home');
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('daily', DailyController::class)->name('daily.index');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::resource('marketing', MarketingController::class)->except('destroy');
        Route::patch('marketing/{marketing}/status', [MarketingController::class, 'status'])->name('marketing.status');
        Route::patch('marketing/{marketing}/reset-password', [MarketingController::class, 'resetPassword'])->name('marketing.reset-password');

        Route::resource('prospects', ProspectController::class)->only(['index', 'show']);
        Route::resource('members', MemberController::class)->only(['index', 'show']);
        Route::patch('members/{member}/approval', [MemberController::class, 'approval'])->name('members.approval');
        Route::resource('operational-reports', OperationalReportController::class)->only(['index', 'show']);
        Route::resource('visit-reports', VisitReportController::class)->only(['index', 'show']);
        Route::resource('schedules', ScheduleController::class)->except(['show']);
        Route::post('operational-recaps/generate', [OperationalRecapController::class, 'generate'])->name('operational-recaps.generate');
        Route::resource('operational-recaps', OperationalRecapController::class)->only(['index', 'show']);
        Route::get('tracking/feed', [TrackingController::class, 'feed'])->name('tracking.feed');
        Route::resource('tracking', TrackingController::class)
            ->only(['index', 'show'])
            ->parameters(['tracking' => 'trackingSession']);
        Route::resource('journeys', JourneyController::class)
            ->only(['index', 'show'])
            ->parameters(['journeys' => 'trackingSession']);

    });
