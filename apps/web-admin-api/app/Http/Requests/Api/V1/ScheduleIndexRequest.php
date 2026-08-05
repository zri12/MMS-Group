<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\DayName;
use App\Enums\ScheduleStatus;
use Illuminate\Validation\Rule;

class ScheduleIndexRequest extends ApiFormRequest
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
            'date' => ['nullable', 'date_format:Y-m-d'],
            'day' => ['nullable', 'string', Rule::in(DayName::values())],
            'status' => ['nullable', 'string', Rule::in(ScheduleStatus::values())],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:'.config('mms.pagination.max_per_page', 100)],
        ];
    }

    public function validatedPerPage(): int
    {
        return $this->perPage();
    }
}
