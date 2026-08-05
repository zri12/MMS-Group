<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ProspectStatus;
use Illuminate\Validation\Rule;

class UpdateProspectRequest extends ApiFormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'phone' => ['sometimes', 'required', 'string', 'max:30'],
            'address' => ['sometimes', 'required', 'string', 'max:5000'],
            'business' => ['sometimes', 'required', 'string', 'max:150'],
            'status' => ['sometimes', 'required', 'string', Rule::in(ProspectStatus::values())],
            'initial_visit_result' => ['sometimes', 'required', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'resort' => ['sometimes', 'required', 'string', 'max:100'],
            'input_date' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'input_time' => ['sometimes', 'required', 'date_format:H:i:s'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
