<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MemberResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'local_uuid' => $this->local_uuid,
            'source_prospect_id' => $this->source_prospect_id,
            'resort' => $this->resort,
            'date' => $this->input_date?->toDateString(),
            'time' => $this->input_time,
            'name' => $this->name,
            'member_number' => $this->member_number,
            'loan_number' => $this->loan_number,
            'address' => $this->address,
            'phone' => $this->phone,
            'business' => $this->business,
            'loan_amount' => $this->loan_amount,
            'installment_amount' => $this->installment_amount,
            'insurance_amount' => $this->insurance_amount,
            'collateral' => $this->collateral,
            'approval_status' => $this->approval_status->value,
            'member_photo_url' => $this->member_photo_path ? Storage::disk('public')->url($this->member_photo_path) : null,
            'latitude' => $this->latitude === null ? null : (float) $this->latitude,
            'longitude' => $this->longitude === null ? null : (float) $this->longitude,
            'location_address' => $this->location_address,
            'sync_status' => $this->sync_status->value,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'rejected_at' => $this->rejected_at?->toIso8601String(),
            'rejection_reason' => $this->rejection_reason,
            'marketing' => $this->whenLoaded('marketingProfile', fn (): array => [
                'id' => $this->marketingProfile->id,
                'code' => $this->marketingProfile->code,
                'name' => $this->marketingProfile->user?->name,
            ]),
        ];
    }
}
