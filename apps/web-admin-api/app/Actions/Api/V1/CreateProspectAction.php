<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Enums\SyncStatus;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use App\Support\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class CreateProspectAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(MarketingProfile $marketing, array $data): Prospect
    {
        return DB::transaction(function () use ($marketing, $data): Prospect {
            $existing = Prospect::query()
                ->with(['marketingProfile.user', 'member'])
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

            return Prospect::query()->create([
                ...$data,
                'marketing_profile_id' => $marketing->id,
                'sync_status' => SyncStatus::Synced,
            ])->load(['marketingProfile.user', 'member']);
        });
    }
}
