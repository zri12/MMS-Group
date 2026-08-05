<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'local_uuid' => $this->local_uuid,
            'schedule_id' => $this->schedule_id,
            'date' => $this->session_date?->toDateString(),
            'day' => $this->day_name?->value,
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at' => $this->ended_at?->toIso8601String(),
            'status' => $this->status->value,
            'distance_meters' => $this->distance_meters,
            'visit_count' => $this->visit_count,
            'points_count' => $this->whenCounted('points'),
            'latest_point' => $this->whenLoaded('latestPoint', fn (): ?array => $this->latestPoint ? (new TrackingPointResource($this->latestPoint))->resolve() : null),
            'marketing' => $this->whenLoaded('marketingProfile', fn (): array => [
                'id' => $this->marketingProfile->id,
                'code' => $this->marketingProfile->code,
                'name' => $this->marketingProfile->user?->name,
            ]),
            'schedule' => $this->whenLoaded('schedule', fn (): ?array => $this->schedule ? [
                'id' => $this->schedule->id,
                'date' => $this->schedule->schedule_date?->toDateString(),
                'agenda' => $this->schedule->agenda,
                'area' => $this->schedule->area,
                'resort' => $this->schedule->resort,
            ] : null),
        ];
    }
}
