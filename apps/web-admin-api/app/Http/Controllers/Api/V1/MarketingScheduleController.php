<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\V1\UpdateScheduleStatusAction;
use App\Enums\ScheduleStatus;
use App\Http\Requests\Api\V1\ScheduleIndexRequest;
use App\Http\Requests\Api\V1\UpdateScheduleStatusRequest;
use App\Http\Resources\Api\V1\MarketingScheduleResource;
use App\Models\MarketingSchedule;
use App\Support\ApiResponse;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketingScheduleController
{
    public function index(ScheduleIndexRequest $request): JsonResponse
    {
        $paginator = $this->baseQuery($request)
            ->when($request->filled('date'), fn (Builder $query) => $query->whereDate('schedule_date', $request->validated('date')))
            ->when($request->filled('day'), fn (Builder $query) => $query->where('day_name', $request->validated('day')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->validated('status')))
            ->orderBy('schedule_date')
            ->orderBy('start_time')
            ->paginate($request->validatedPerPage())
            ->withQueryString();

        return ApiResponse::paginated(
            paginator: $paginator,
            data: MarketingScheduleResource::collection($paginator->getCollection())->resolve(),
            message: 'Jadwal berhasil dimuat.',
        );
    }

    public function today(Request $request): JsonResponse
    {
        $today = CarbonImmutable::now(config('app.timezone'))->toDateString();

        $schedules = $this->baseQuery($request)
            ->whereDate('schedule_date', $today)
            ->orderBy('start_time')
            ->get();

        return ApiResponse::success(
            message: 'Jadwal hari ini berhasil dimuat.',
            data: MarketingScheduleResource::collection($schedules)->resolve(),
        );
    }

    public function show(Request $request, MarketingSchedule $schedule): JsonResponse
    {
        if ($schedule->marketing_profile_id !== $request->user()->marketingProfile->id) {
            return ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            );
        }

        return ApiResponse::success(
            message: 'Detail jadwal berhasil dimuat.',
            data: (new MarketingScheduleResource($schedule->load(['marketingProfile.user', 'prospect'])))->resolve(),
        );
    }

    public function updateStatus(
        UpdateScheduleStatusRequest $request,
        MarketingSchedule $schedule,
        UpdateScheduleStatusAction $action,
    ): JsonResponse {
        if ($schedule->marketing_profile_id !== $request->user()->marketingProfile->id) {
            return ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            );
        }

        $schedule = $action->execute(
            schedule: $schedule,
            targetStatus: ScheduleStatus::from($request->validated('status')),
        );

        return ApiResponse::success(
            message: 'Status jadwal berhasil diperbarui.',
            data: (new MarketingScheduleResource($schedule))->resolve(),
        );
    }

    private function baseQuery(Request $request): Builder
    {
        return MarketingSchedule::query()
            ->with(['marketingProfile.user', 'prospect'])
            ->where('marketing_profile_id', $request->user()->marketingProfile->id);
    }
}
