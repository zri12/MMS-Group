<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\MarketingProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetMarketingPasswordAction
{
    public function execute(MarketingProfile $marketing, string $password): MarketingProfile
    {
        return DB::transaction(function () use ($marketing, $password): MarketingProfile {
            $marketing->user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            $marketing->user->tokens()->delete();

            return $marketing->refresh()->load('user');
        });
    }
}
