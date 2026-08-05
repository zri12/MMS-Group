<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\MemberApprovalStatus;
use Illuminate\Validation\Rule;

class MemberIndexRequest extends ApiFormRequest
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
            'approval_status' => ['nullable', 'string', Rule::in(MemberApprovalStatus::values())],
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:'.config('mms.pagination.max_per_page', 100)],
        ];
    }

    public function validatedPerPage(): int
    {
        return $this->perPage();
    }
}
