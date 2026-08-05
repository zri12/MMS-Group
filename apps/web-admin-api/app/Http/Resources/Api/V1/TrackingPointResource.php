<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingPointResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'local_uuid' => $this->local_uuid,
            'tracking_session_id' => $this->tracking_session_id,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'accuracy_meters' => $this->accuracy_meters === null ? null : (float) $this->accuracy_meters,
            'speed_mps' => $this->speed_mps === null ? null : (float) $this->speed_mps,
            'heading' => $this->heading === null ? null : (float) $this->heading,
            'altitude_meters' => $this->altitude_meters === null ? null : (float) $this->altitude_meters,
            'point_type' => $this->point_type->value,
            'recorded_at' => $this->recorded_at?->toIso8601String(),
            'received_at' => $this->received_at?->toIso8601String(),
        ];
    }
}
