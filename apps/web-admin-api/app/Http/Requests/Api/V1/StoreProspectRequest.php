<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ProspectStatus;
use Illuminate\Validation\Rule;

class StoreProspectRequest extends ApiFormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:5000'],
            'business' => ['required', 'string', 'max:150'],
            'status' => ['required', 'string', Rule::in(ProspectStatus::values())],
            'initial_visit_result' => ['required', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'resort' => ['required', 'string', 'max:100'],
            'input_date' => ['required', 'date_format:Y-m-d'],
            'input_time' => ['required', 'date_format:H:i:s'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
