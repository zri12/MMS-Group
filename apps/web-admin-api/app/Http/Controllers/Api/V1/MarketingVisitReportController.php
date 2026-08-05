<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\V1\CreateVisitReportAction;
use App\Http\Requests\Api\V1\StoreVisitReportRequest;
use App\Http\Requests\Api\V1\VisitReportIndexRequest;
use App\Http\Resources\Api\V1\VisitReportResource;
use App\Models\VisitReport;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketingVisitReportController
{
    public function index(VisitReportIndexRequest $request): JsonResponse
    {
        $paginator = VisitReport::query()
            ->with(['prospect', 'marketingProfile.user'])
            ->where('marketing_profile_id', $request->user()->marketingProfile->id)
            ->when($request->filled('prospect_id'), fn (Builder $query) => $query->where('prospect_id', $request->validated('prospect_id')))
            ->when($request->filled('result'), fn (Builder $query) => $query->where('visit_result', $request->validated('result')))
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('visit_date', '>=', $request->validated('date_from')))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('visit_date', '<=', $request->validated('date_to')))
            ->latest('visit_date')
            ->latest('visit_time')
            ->paginate($request->validatedPerPage())
            ->withQueryString();

        return ApiResponse::paginated(
            paginator: $paginator,
            data: VisitReportResource::collection($paginator->getCollection())->resolve(),
            message: 'Laporan kunjungan berhasil dimuat.',
        );
    }

    public function store(StoreVisitReportRequest $request, CreateVisitReportAction $action): JsonResponse
    {
        $report = $action->execute(
            marketing: $request->user()->marketingProfile,
            data: $request->validated(),
            photo: $request->file('photo'),
        );

        return ApiResponse::created(
            message: 'Laporan kunjungan berhasil disimpan.',
            data: (new VisitReportResource($report))->resolve(),
        );
    }

    public function show(Request $request, VisitReport $visitReport): JsonResponse
    {
        if ($visitReport->marketing_profile_id !== $request->user()->marketingProfile->id) {
            return ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            );
        }

        return ApiResponse::success(
            message: 'Detail laporan kunjungan berhasil dimuat.',
            data: (new VisitReportResource($visitReport->load(['prospect', 'marketingProfile.user'])))->resolve(),
        );
    }
}
