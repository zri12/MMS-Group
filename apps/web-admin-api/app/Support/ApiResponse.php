<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Helper untuk membentuk response API dengan format envelope yang konsisten.
 *
 * Format dipakai oleh seluruh endpoint aplikasi marketing.
 */
final class ApiResponse
{
    /**
     * Response sukses dengan data.
     *
     * @param  string  $message  Pesan sukses dalam Bahasa Indonesia.
     * @param  mixed  $data  Data yang dikembalikan.
     * @param  int  $status  HTTP status code (default 200).
     */
    public static function success(
        string $message = 'Data berhasil dimuat.',
        mixed $data = null,
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Response sukses untuk resource yang baru dibuat.
     *
     * @param  string  $message  Pesan sukses dalam Bahasa Indonesia.
     * @param  mixed  $data  Data resource yang dibuat.
     */
    public static function created(
        string $message = 'Data berhasil dibuat.',
        mixed $data = null,
    ): JsonResponse {
        return self::success(
            message: $message,
            data: $data,
            status: 201,
        );
    }

    /**
     * Response error umum.
     *
     * @param  string  $message  Pesan error dalam Bahasa Indonesia.
     * @param  mixed  $errors  Detail error (opsional).
     * @param  int  $status  HTTP status code (default 500).
     */
    public static function error(
        string $message = 'Terjadi kesalahan.',
        mixed $errors = null,
        int $status = 500,
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    /**
     * Response collection dengan metadata pagination standar.
     *
     * @param  mixed  $data  Data collection yang sudah diubah lewat API Resource.
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        mixed $data,
        string $message = 'Data berhasil dimuat.',
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Response error validasi (422 Unprocessable Entity).
     *
     * @param  array<string, list<string>>  $errors  Daftar error per field.
     * @param  string  $message  Pesan error validasi.
     */
    public static function validationError(
        array $errors,
        string $message = 'Data yang diberikan tidak valid.',
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Response tanpa konten (204 No Content).
     *
     * Mengikuti standar HTTP: tidak mengirim body.
     */
    public static function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
