<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Models\MarketingProfile;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use App\Support\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StoreTrackingPointsAction
{
    /**
     * @param  list<array<string, mixed>>  $points
     * @return array{points: Collection<int, TrackingPoint>, created_count: int, duplicate_count: int}
     */
    public function execute(MarketingProfile $marketing, TrackingSession $session, array $points): array
    {
        return DB::transaction(function () use ($marketing, $session, $points): array {
            $lockedSession = TrackingSession::query()
                ->whereKey($session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedSession->marketing_profile_id !== $marketing->id) {
                throw new HttpResponseException(ApiResponse::error(
                    message: 'Data tidak ditemukan.',
                    status: 404,
                ));
            }

            $created = 0;
            $duplicates = 0;
            $stored = collect();

            foreach ($points as $point) {
                $existing = TrackingPoint::query()
                    ->with('trackingSession')
                    ->where('local_uuid', $point['local_uuid'])
                    ->first();

                if ($existing) {
                    if ($existing->tracking_session_id !== $lockedSession->id || $existing->trackingSession->marketing_profile_id !== $marketing->id) {
                        throw new HttpResponseException(ApiResponse::error(
                            message: 'Data tidak dapat diproses.',
                            status: 409,
                        ));
                    }

                    $duplicates++;
                    $stored->push($existing);

                    continue;
                }

                $stored->push(TrackingPoint::query()->create([
                    'local_uuid' => $point['local_uuid'],
                    'tracking_session_id' => $lockedSession->id,
                    'latitude' => $point['latitude'],
                    'longitude' => $point['longitude'],
                    'accuracy_meters' => $point['accuracy_meters'] ?? null,
                    'speed_mps' => $point['speed_mps'] ?? null,
                    'heading' => $point['heading'] ?? null,
                    'altitude_meters' => $point['altitude_meters'] ?? null,
                    'point_type' => $point['point_type'],
                    'recorded_at' => $point['recorded_at'],
                    'received_at' => now(),
                ]));

                $created++;
            }

            return [
                'points' => $stored,
                'created_count' => $created,
                'duplicate_count' => $duplicates,
            ];
        });
    }
}
