<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Enums\UserRole;
use App\Models\MarketingProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateMarketingAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): MarketingProfile
    {
        return DB::transaction(function () use ($data): MarketingProfile {
            $user = User::query()->create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make((string) $data['password']),
                'role' => UserRole::Marketing,
                'is_active' => true,
            ]);

            $profile = MarketingProfile::query()->create([
                'user_id' => $user->id,
                'code' => $data['code'],
                'phone' => $data['phone'] ?? null,
                'area' => $data['area'],
                'profile_photo_path' => $this->storePhoto($data['profile_photo'] ?? null),
            ]);

            $this->syncWorkDays($profile, $data['work_days']);

            return $profile->load(['user', 'workDays']);
        });
    }

    /**
     * @param  list<string>  $workDays
     */
    private function syncWorkDays(MarketingProfile $profile, array $workDays): void
    {
        foreach (array_values($workDays) as $dayName) {
            $profile->workDays()->create([
                'day_name' => $dayName,
            ]);
        }
    }

    private function storePhoto(mixed $photo): ?string
    {
        if (! $photo instanceof UploadedFile) {
            return null;
        }

        return $photo->store('marketing-profiles', 'public');
    }
}
