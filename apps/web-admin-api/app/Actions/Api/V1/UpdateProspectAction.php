<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Enums\SyncStatus;
use App\Models\Prospect;
use Illuminate\Support\Facades\DB;

class UpdateProspectAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Prospect $prospect, array $data): Prospect
    {
        return DB::transaction(function () use ($prospect, $data): Prospect {
            $prospect->forceFill([
                ...$data,
                'sync_status' => SyncStatus::Synced,
            ])->save();

            return $prospect->refresh()->load(['marketingProfile.user', 'member']);
        });
    }
}
