<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * Health check endpoint untuk memverifikasi bahwa API aktif.
 *
 * Tidak memerlukan autentikasi.
 * Tidak mengakses database.
 */
class HealthController
{
    /**
     * Mengembalikan status layanan API.
     */
    public function __invoke(): JsonResponse
    {
        return ApiResponse::success(
            message: 'MMS Monitoring API aktif.',
            data: [
                'service' => config('mms.api_name'),
                'version' => config('mms.api_version'),
                'timestamp' => now()->toIso8601String(),
            ],
        );
    }
}
