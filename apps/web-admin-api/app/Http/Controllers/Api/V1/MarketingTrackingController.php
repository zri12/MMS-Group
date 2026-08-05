<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\V1\StartTrackingSessionAction;
use App\Actions\Api\V1\StopTrackingSessionAction;
use App\Actions\Api\V1\StoreTrackingPointsAction;
use App\Enums\TrackingStatus;
use App\Http\Requests\Api\V1\StartTrackingSessionRequest;
use App\Http\Requests\Api\V1\StopTrackingSessionRequest;
use App\Http\Requests\Api\V1\StoreTrackingPointRequest;
use App\Http\Requests\Api\V1\StoreTrackingPointsBatchRequest;
use App\Http\Requests\Api\V1\TrackingSessionIndexRequest;
use App\Http\Resources\Api\V1\TrackingPointResource;
use App\Http\Resources\Api\V1\TrackingSessionResource;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MarketingTrackingController
{
    public function index(TrackingSessionIndexRequest $request): JsonResponse
    {
        $paginator = TrackingSession::query()
            ->with(['marketingProfile.user', 'schedule', 'latestPoint'])
            ->withCount('points')
            ->where('marketing_profile_id', $request->user()->marketingProfile->id)
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('session_date', '>=', $request->validated('date_from')))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('session_date', '<=', $request->validated('date_to')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->validated('status')))
            ->latest('started_at')
            ->paginate($request->validatedPerPage())
            ->withQueryString();

        return ApiResponse::paginated(
            paginator: $paginator,
            data: TrackingSessionResource::collection($paginator->getCollection())->resolve(),
            message: 'Riwayat tracking berhasil dimuat.',
        );
    }

    public function current(Request $request): JsonResponse
    {
        $session = TrackingSession::query()
            ->with(['marketingProfile.user', 'schedule', 'latestPoint'])
            ->withCount('points')
            ->where('marketing_profile_id', $request->user()->marketingProfile->id)
            ->where('status', TrackingStatus::Active->value)
            ->latest('started_at')
            ->first();

        return ApiResponse::success(
            message: 'Tracking aktif berhasil dimuat.',
            data: $session ? (new TrackingSessionResource($session))->resolve() : null,
        );
    }

    public function start(StartTrackingSessionRequest $request, StartTrackingSessionAction $action): JsonResponse
    {
        $session = $action->execute(
            marketing: $request->user()->marketingProfile,
            data: $request->validated(),
        );

        return ApiResponse::created(
            message: 'Tracking berhasil dimulai.',
            data: [
                'session_id' => $session->id,
                'session' => (new TrackingSessionResource($session))->resolve(),
            ],
        );
    }

    public function show(Request $request, TrackingSession $trackingSession): JsonResponse
    {
        if ($trackingSession->marketing_profile_id !== $request->user()->marketingProfile->id) {
            return ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            );
        }

        $trackingSession->load(['marketingProfile.user', 'schedule', 'latestPoint'])
            ->loadCount('points');
        $points = $trackingSession->points()
            ->orderBy('recorded_at')
            ->get();

        return ApiResponse::success(
            message: 'Detail tracking berhasil dimuat.',
            data: [
                'session' => (new TrackingSessionResource($trackingSession))->resolve(),
                'points' => TrackingPointResource::collection($points)->resolve(),
            ],
        );
    }

    public function storePoint(StoreTrackingPointRequest $request, TrackingSession $trackingSession, StoreTrackingPointsAction $action): JsonResponse
    {
        $result = $action->execute(
            marketing: $request->user()->marketingProfile,
            session: $trackingSession,
            points: [$request->validated()],
        );

        return ApiResponse::created(
            message: 'Titik tracking berhasil disimpan.',
            data: $this->pointPayload($result),
        );
    }

    public function storePointBatch(StoreTrackingPointsBatchRequest $request, TrackingSession $trackingSession, StoreTrackingPointsAction $action): JsonResponse
    {
        $result = $action->execute(
            marketing: $request->user()->marketingProfile,
            session: $trackingSession,
            points: $request->validated('points'),
        );

        return ApiResponse::created(
            message: 'Titik tracking berhasil disimpan.',
            data: $this->pointPayload($result),
        );
    }

    public function stop(StopTrackingSessionRequest $request, TrackingSession $trackingSession, StopTrackingSessionAction $action): JsonResponse
    {
        $session = $action->execute(
            marketing: $request->user()->marketingProfile,
            session: $trackingSession,
            data: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Tracking berhasil selesai.',
            data: (new TrackingSessionResource($session))->resolve(),
        );
    }

    /**
     * @param  array{points: Collection<int, TrackingPoint>, created_count: int, duplicate_count: int}  $result
     * @return array<string, mixed>
     */
    private function pointPayload(array $result): array
    {
        return [
            'created_count' => $result['created_count'],
            'duplicate_count' => $result['duplicate_count'],
            'points' => TrackingPointResource::collection($result['points'])->resolve(),
        ];
    }
}
