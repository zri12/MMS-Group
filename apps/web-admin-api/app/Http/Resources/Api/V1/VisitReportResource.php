<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VisitReportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'local_uuid' => $this->local_uuid,
            'prospect_id' => $this->prospect_id,
            'date' => $this->visit_date?->toDateString(),
            'time' => $this->visit_time,
            'day' => $this->day_name?->value,
            'visit_purpose' => $this->visit_purpose,
            'visit_result' => $this->visit_result->value,
            'prospect_status' => $this->prospect_status->value,
            'notes' => $this->notes,
            'follow_up_date' => $this->follow_up_date?->toDateString(),
            'photo_url' => $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null,
            'photo_caption' => $this->photo_caption,
            'resort' => $this->resort,
            'latitude' => $this->latitude === null ? null : (float) $this->latitude,
            'longitude' => $this->longitude === null ? null : (float) $this->longitude,
            'location_address' => $this->location_address,
            'sync_status' => $this->sync_status->value,
            'prospect' => $this->whenLoaded('prospect', fn (): array => [
                'id' => $this->prospect->id,
                'name' => $this->prospect->name,
                'phone' => $this->prospect->phone,
                'status' => $this->prospect->status->value,
            ]),
            'marketing' => $this->whenLoaded('marketingProfile', fn (): array => [
                'id' => $this->marketingProfile->id,
                'code' => $this->marketingProfile->code,
                'name' => $this->marketingProfile->user?->name,
            ]),
        ];
    }
}
