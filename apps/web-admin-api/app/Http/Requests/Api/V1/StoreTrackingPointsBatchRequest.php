<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\TrackingPointType;
use Illuminate\Validation\Rule;

class StoreTrackingPointsBatchRequest extends ApiFormRequest
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
            'points' => ['required', 'array', 'min:1', 'max:'.config('mms.tracking.max_batch_points', 100)],
            'points.*.local_uuid' => ['required', 'uuid', 'distinct'],
            'points.*.latitude' => ['required', 'numeric', 'between:-90,90'],
            'points.*.longitude' => ['required', 'numeric', 'between:-180,180'],
            'points.*.accuracy_meters' => ['nullable', 'numeric', 'min:0'],
            'points.*.speed_mps' => ['nullable', 'numeric', 'min:0'],
            'points.*.heading' => ['nullable', 'numeric', 'between:0,360'],
            'points.*.altitude_meters' => ['nullable', 'numeric'],
            'points.*.point_type' => ['required', 'string', Rule::in(TrackingPointType::values())],
            'points.*.recorded_at' => ['required', 'date'],
        ];
    }
}
