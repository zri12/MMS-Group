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

        $number = (int) preg_replace('/\D+/', '', $profile->code);

        if ($number >= 1 && $number <= 13) {
            return asset('profiles/marketing-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT).'.jpg');
        }

        return '';
    }

    public static function member(Member $member): string
    {
        if ($member->member_photo_path) {
            return Storage::disk('public')->url($member->member_photo_path);
        }

        $number = (($member->id - 1) % 16) + 1;

        return asset('profiles/member-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT).'.jpg');
    }
}
