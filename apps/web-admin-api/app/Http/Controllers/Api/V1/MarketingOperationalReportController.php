<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\V1\CreateOperationalReportAction;
use App\Http\Requests\Api\V1\OperationalReportIndexRequest;
use App\Http\Requests\Api\V1\StoreOperationalReportRequest;
use App\Http\Resources\Api\V1\DailyOperationalReportResource;
use App\Models\DailyOperationalReport;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketingOperationalReportController
{
    public function index(OperationalReportIndexRequest $request): JsonResponse
    {
        $paginator = DailyOperationalReport::query()
            ->with('marketingProfile.user')
            ->where('marketing_profile_id', $request->user()->marketingProfile->id)
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('report_date', '>=', $request->validated('date_from')))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('report_date', '<=', $request->validated('date_to')))
            ->latest('report_date')
            ->latest('report_time')
            ->paginate($request->validatedPerPage())
            ->withQueryString();

        return ApiResponse::paginated(
            paginator: $paginator,
            data: DailyOperationalReportResource::collection($paginator->getCollection())->resolve(),
            message: 'Laporan operasional berhasil dimuat.',
        );
    }

    public function store(StoreOperationalReportRequest $request, CreateOperationalReportAction $action): JsonResponse
    {
        $report = $action->execute(
            marketing: $request->user()->marketingProfile,
            data: $request->validated(),
        );

        return ApiResponse::created(
            message: 'Laporan operasional berhasil disimpan.',
            data: (new DailyOperationalReportResource($report))->resolve(),
        );
    }

    public function show(Request $request, DailyOperationalReport $operationalReport): JsonResponse
    {
        if ($operationalReport->marketing_profile_id !== $request->user()->marketingProfile->id) {
            return ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            );
        }

        return ApiResponse::success(
            message: 'Detail laporan operasional berhasil dimuat.',
            data: (new DailyOperationalReportResource($operationalReport->load('marketingProfile.user')))->resolve(),
        );
    }
}
