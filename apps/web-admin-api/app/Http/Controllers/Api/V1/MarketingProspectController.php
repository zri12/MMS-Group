<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\V1\CreateProspectAction;
use App\Actions\Api\V1\UpdateProspectAction;
use App\Http\Requests\Api\V1\ProspectIndexRequest;
use App\Http\Requests\Api\V1\StoreProspectRequest;
use App\Http\Requests\Api\V1\UpdateProspectRequest;
use App\Http\Resources\Api\V1\ProspectResource;
use App\Models\Prospect;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketingProspectController
{
    public function index(ProspectIndexRequest $request): JsonResponse
    {
        $paginator = Prospect::query()
            ->with(['marketingProfile.user', 'member'])
            ->where('marketing_profile_id', $request->user()->marketingProfile->id)
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->validated('status')))
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('business', 'like', $search)
                        ->orWhere('resort', 'like', $search);
                });
            })
            ->latest('input_date')
            ->latest('input_time')
            ->paginate($request->validatedPerPage())
            ->withQueryString();

        return ApiResponse::paginated(
            paginator: $paginator,
            data: ProspectResource::collection($paginator->getCollection())->resolve(),
            message: 'Prospek berhasil dimuat.',
        );
    }

    public function store(StoreProspectRequest $request, CreateProspectAction $action): JsonResponse
    {
        $prospect = $action->execute($request->user()->marketingProfile, $request->validated());

        return ApiResponse::created(
            message: 'Prospek berhasil disimpan.',
            data: (new ProspectResource($prospect))->resolve(),
        );
    }

    public function show(Request $request, Prospect $prospect): JsonResponse
    {
        if ($prospect->marketing_profile_id !== $request->user()->marketingProfile->id) {
            return ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            );
        }

        return ApiResponse::success(
            message: 'Detail prospek berhasil dimuat.',
            data: (new ProspectResource($prospect->load(['marketingProfile.user', 'member'])))->resolve(),
        );
    }

    public function update(UpdateProspectRequest $request, Prospect $prospect, UpdateProspectAction $action): JsonResponse
    {
        if ($prospect->marketing_profile_id !== $request->user()->marketingProfile->id) {
            return ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            );
        }

        $prospect = $action->execute($prospect, $request->validated());

        return ApiResponse::success(
            message: 'Prospek berhasil diperbarui.',
            data: (new ProspectResource($prospect))->resolve(),
        );
    }
}
