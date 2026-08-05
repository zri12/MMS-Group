<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\TrackingPointType;
use Illuminate\Validation\Rule;

class StoreTrackingPointRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMarketing() === true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'local_uuid' => ['required', 'uuid'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0'],
            'speed_mps' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'between:0,360'],
            'altitude_meters' => ['nullable', 'numeric'],
            'point_type' => ['required', 'string', Rule::in(TrackingPointType::values())],
            'recorded_at' => ['required', 'date'],
        ];
    }
}
