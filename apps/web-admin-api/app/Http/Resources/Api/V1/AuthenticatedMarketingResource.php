<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AuthenticatedMarketingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $profile = $this->marketingProfile;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role->value,
            'is_active' => $this->is_active,
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'marketing' => $profile ? [
                'id' => $profile->id,
                'code' => $profile->code,
                'phone' => $profile->phone,
                'area' => $profile->area,
                'profile_photo_url' => $profile->profile_photo_path
                    ? Storage::disk('public')->url($profile->profile_photo_path)
                    : null,
                'work_days' => $profile->workDays
                    ->pluck('day_name')
                    ->map(fn ($dayName): string => is_string($dayName) ? $dayName : $dayName->value)
                    ->values()
                    ->all(),
            ] : null,
        ];
    }
}
