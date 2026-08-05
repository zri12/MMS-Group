<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\VisitResult;
use Illuminate\Validation\Rule;

class VisitReportIndexRequest extends ApiFormRequest
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
            'prospect_id' => ['nullable', 'integer', 'exists:prospects,id'],
            'result' => ['nullable', 'string', Rule::in(VisitResult::values())],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:'.config('mms.pagination.max_per_page', 100)],
        ];
    }

    public function validatedPerPage(): int
    {
        return $this->perPage();
    }
}
