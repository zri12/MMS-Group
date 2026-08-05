<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyOperationalReportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'local_uuid' => $this->local_uuid,
            'date' => $this->report_date?->toDateString(),
            'time' => $this->report_time,
            'day' => $this->day_name?->value,
            'resort' => $this->resort,
            'storting' => $this->storting,
            'insurance_amount' => $this->insurance_amount,
            'drop' => $this->drop_amount,
            'withdrawal_saving' => $this->withdrawal_saving,
            'previous_target_amount' => $this->previous_target_amount,
            'previous_target_people' => $this->previous_target_people,
            'incoming_target_amount' => $this->incoming_target_amount,
            'incoming_target_people' => $this->incoming_target_people,
            'outgoing_target_amount' => $this->outgoing_target_amount,
            'outgoing_target_people' => $this->outgoing_target_people,
            'total_target_amount' => $this->total_target_amount,
            'total_target_people' => $this->total_target_people,
            'new_drop' => $this->new_drop,
            'continued_drop' => $this->continued_drop,
            'notes' => $this->notes,
            'sync_status' => $this->sync_status->value,
            'marketing' => $this->whenLoaded('marketingProfile', fn (): array => [
                'id' => $this->marketingProfile->id,
                'code' => $this->marketingProfile->code,
                'name' => $this->marketingProfile->user?->name,
            ]),
        ];
    }
}
