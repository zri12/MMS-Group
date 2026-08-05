<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Laravel otomatis memberikan prefix /api pada seluruh route di file ini.
| Route versioned dimuat dari routes/api/v1.php.
|
*/

// API Versi 1 - prefix akhir: /api/v1
Route::prefix('v1')->group(function (): void {
    require __DIR__.'/api/v1.php';
});
