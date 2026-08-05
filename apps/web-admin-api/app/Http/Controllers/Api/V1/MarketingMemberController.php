<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Api\V1\CreateMemberAction;
use App\Http\Requests\Api\V1\MemberIndexRequest;
use App\Http\Requests\Api\V1\StoreMemberRequest;
use App\Http\Resources\Api\V1\MemberResource;
use App\Models\Member;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketingMemberController
{
    public function index(MemberIndexRequest $request): JsonResponse
    {
        $paginator = Member::query()
            ->with(['marketingProfile.user', 'sourceProspect'])
            ->where('marketing_profile_id', $request->user()->marketingProfile->id)
            ->when($request->filled('approval_status'), fn (Builder $query) => $query->where('approval_status', $request->validated('approval_status')))
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('member_number', 'like', $search)
                        ->orWhere('loan_number', 'like', $search);
                });
            })
            ->latest('input_date')
            ->latest('input_time')
            ->paginate($request->validatedPerPage())
            ->withQueryString();

        return ApiResponse::paginated(
            paginator: $paginator,
            data: MemberResource::collection($paginator->getCollection())->resolve(),
            message: 'Anggota berhasil dimuat.',
        );
    }

    public function store(StoreMemberRequest $request, CreateMemberAction $action): JsonResponse
    {
        $member = $action->execute(
            marketing: $request->user()->marketingProfile,
            data: $request->validated(),
            photo: $request->file('member_photo'),
        );

        return ApiResponse::created(
            message: 'Anggota berhasil disimpan.',
            data: (new MemberResource($member))->resolve(),
        );
    }

    public function show(Request $request, Member $member): JsonResponse
    {
        if ($member->marketing_profile_id !== $request->user()->marketingProfile->id) {
            return ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            );
        }

        return ApiResponse::success(
            message: 'Detail anggota berhasil dimuat.',
            data: (new MemberResource($member->load(['marketingProfile.user', 'sourceProspect'])))->resolve(),
        );
    }
}
