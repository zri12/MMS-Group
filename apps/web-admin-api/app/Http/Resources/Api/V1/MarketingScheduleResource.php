<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketingScheduleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'marketing_id' => $this->marketing_profile_id,
            'prospect_id' => $this->prospect_id,
            'day' => $this->day_name->value,
            'date' => $this->schedule_date?->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'consumer_name' => $this->consumer_name_snapshot,
            'agenda' => $this->agenda,
            'area' => $this->area,
            'resort' => $this->resort,
            'destination' => $this->destination,
            'note' => $this->note,
            'status' => $this->status->value,
            'marketing' => $this->whenLoaded('marketingProfile', fn (): array => [
                'id' => $this->marketingProfile->id,
                'code' => $this->marketingProfile->code,
                'name' => $this->marketingProfile->user?->name,
            ]),
            'prospect' => $this->whenLoaded('prospect', fn (): ?array => $this->prospect ? [
                'id' => $this->prospect->id,
                'name' => $this->prospect->name,
                'phone' => $this->prospect->phone,
            ] : null),
        ];
    }
}
