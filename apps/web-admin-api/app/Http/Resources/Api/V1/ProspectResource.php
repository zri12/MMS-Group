<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProspectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'local_uuid' => $this->local_uuid,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'business' => $this->business,
            'status' => $this->status->value,
            'initial_visit_result' => $this->initial_visit_result,
            'notes' => $this->notes,
            'resort' => $this->resort,
            'input_date' => $this->input_date?->toDateString(),
            'input_time' => $this->input_time,
            'latitude' => $this->latitude === null ? null : (float) $this->latitude,
            'longitude' => $this->longitude === null ? null : (float) $this->longitude,
            'location_address' => $this->location_address,
            'sync_status' => $this->sync_status->value,
            'linked_member_id' => $this->whenLoaded('member', fn () => $this->member?->id),
            'marketing' => $this->whenLoaded('marketingProfile', fn (): array => [
                'id' => $this->marketingProfile->id,
                'code' => $this->marketingProfile->code,
                'name' => $this->marketingProfile->user?->name,
            ]),
        ];
    }
}
