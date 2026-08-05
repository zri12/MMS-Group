<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Enums\TrackingStatus;
use App\Models\MarketingProfile;
use App\Models\TrackingSession;
use App\Support\ApiResponse;
use Carbon\CarbonImmutable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StopTrackingSessionAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(MarketingProfile $marketing, TrackingSession $session, array $data): TrackingSession
    {
        return DB::transaction(function () use ($marketing, $session, $data): TrackingSession {
            $locked = TrackingSession::query()
                ->whereKey($session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->marketing_profile_id !== $marketing->id) {
                throw new HttpResponseException(ApiResponse::error(
                    message: 'Data tidak ditemukan.',
                    status: 404,
                ));
            }

            $endedAt = CarbonImmutable::parse($data['ended_at']);

            if ($locked->status !== TrackingStatus::Active) {
                return $locked->refresh()
                    ->load(['marketingProfile.user', 'schedule', 'latestPoint'])
                    ->loadCount('points');
            }

            if ($endedAt->lt($locked->started_at)) {
                throw ValidationException::withMessages([
                    'ended_at' => 'Waktu selesai tidak boleh lebih awal dari waktu mulai.',
                ]);
            }

            $locked->forceFill([
                'ended_at' => $endedAt,
                'status' => TrackingStatus::Offline,
                'distance_meters' => $data['distance_meters'],
                'visit_count' => $data['visit_count'],
            ])->save();

            return $locked->refresh()
                ->load(['marketingProfile.user', 'schedule', 'latestPoint'])
                ->loadCount('points');
        });
    }
}
