<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\DayName;
use App\Enums\ProspectStatus;
use App\Enums\VisitResult;
use Illuminate\Validation\Rule;

class StoreVisitReportRequest extends ApiFormRequest
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
            'prospect_id' => ['required', 'integer', 'exists:prospects,id'],
            'visit_purpose' => ['required', 'string', 'max:255'],
            'visit_result' => ['required', 'string', Rule::in(VisitResult::values())],
            'prospect_status' => ['required', 'string', Rule::in(ProspectStatus::values())],
            'notes' => ['nullable', 'string', 'max:5000'],
            'follow_up_date' => ['nullable', 'date_format:Y-m-d'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('mms.uploads.max_image_kb', 2048)],
            'photo_caption' => ['nullable', 'string', 'max:255'],
            'resort' => ['required', 'string', 'max:100'],
            'day' => ['required', 'string', Rule::in(DayName::operationalValues())],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i:s'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
