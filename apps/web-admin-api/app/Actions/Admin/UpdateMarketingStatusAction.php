<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\MarketingProfile;
use Illuminate\Support\Facades\DB;

class UpdateMarketingStatusAction
{
    public function execute(MarketingProfile $marketing, bool $isActive): MarketingProfile
    {
        return DB::transaction(function () use ($marketing, $isActive): MarketingProfile {
            $marketing->user->forceFill([
                'is_active' => $isActive,
            ])->save();

            if (! $isActive) {
                $marketing->user->tokens()->delete();
            }

            return $marketing->refresh()->load('user');
        });
    }
}
