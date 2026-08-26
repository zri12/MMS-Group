<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\MarketingProfile;
use App\Models\Member;
use Illuminate\Support\Facades\Storage;

class ProfilePhoto
{
    public static function marketing(MarketingProfile $profile): string
    {
        if ($profile->profile_photo_path) {
            return Storage::disk('public')->url($profile->profile_photo_path);
        }

        return '';
    }

    public static function member(Member $member): string
    {
        if ($member->member_photo_path) {
            return Storage::disk('public')->url($member->member_photo_path);
        }

        return '';
    }
}
