<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Enums\MemberApprovalStatus;
use App\Enums\SyncStatus;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\Prospect;
use App\Support\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateMemberAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(MarketingProfile $marketing, array $data, ?UploadedFile $photo = null): Member
    {
        return DB::transaction(function () use ($marketing, $data, $photo): Member {
            $existing = Member::query()
                ->with(['marketingProfile.user', 'sourceProspect'])
                ->where('local_uuid', $data['local_uuid'])
                ->first();

            if ($existing) {
                if ($existing->marketing_profile_id !== $marketing->id) {
                    throw new HttpResponseException(ApiResponse::error(
                        message: 'Data tidak dapat diproses.',
                        status: 409,
                    ));
                }

                return $existing;
            }

            if (isset($data['source_prospect_id'])) {
                $sourceProspect = Prospect::query()->find($data['source_prospect_id']);

                if (! $sourceProspect || $sourceProspect->marketing_profile_id !== $marketing->id) {
                    throw new HttpResponseException(ApiResponse::error(
                        message: 'Data tidak ditemukan.',
                        status: 404,
                    ));
                }
            }

            $photoPath = $photo?->store('members', 'public');

            return Member::query()->create([
                'local_uuid' => $data['local_uuid'],
                'marketing_profile_id' => $marketing->id,
                'source_prospect_id' => $data['source_prospect_id'] ?? null,
                'resort' => $data['resort'],
                'input_date' => $data['date'],
                'input_time' => $data['time'],
                'name' => $data['name'],
                'member_number' => $data['member_number'],
                'loan_number' => $data['loan_number'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'business' => $data['business'],
                'loan_amount' => $data['loan_amount'],
                'installment_amount' => $data['installment_amount'],
                'insurance_amount' => $data['insurance_amount'],
                'collateral' => $data['collateral'],
                'approval_status' => MemberApprovalStatus::Pending,
                'member_photo_path' => $photoPath,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'location_address' => $data['location_address'] ?? null,
                'sync_status' => SyncStatus::Synced,
            ])->load(['marketingProfile.user', 'sourceProspect']);
        });
    }
}
