<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\MarketingProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateMarketingAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(MarketingProfile $marketing, array $data): MarketingProfile
    {
        return DB::transaction(function () use ($marketing, $data): MarketingProfile {
            $marketing->user->forceFill([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
            ])->save();

            $profileData = [
                'code' => $data['code'],
                'phone' => $data['phone'] ?? null,
                'area' => $data['area'],
            ];

            if (($data['profile_photo'] ?? null) instanceof UploadedFile) {
                if ($marketing->profile_photo_path) {
                    Storage::disk('public')->delete($marketing->profile_photo_path);
                }

                $profileData['profile_photo_path'] = $data['profile_photo']->store('marketing-profiles', 'public');
            }

            $marketing->forceFill($profileData)->save();

            $marketing->workDays()->delete();

            foreach (array_values($data['work_days']) as $dayName) {
                $marketing->workDays()->create([
                    'day_name' => $dayName,
                ]);
            }

            return $marketing->refresh()->load(['user', 'workDays']);
        });
    }
}
