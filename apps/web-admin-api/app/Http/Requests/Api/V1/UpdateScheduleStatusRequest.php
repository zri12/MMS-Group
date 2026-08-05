<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ScheduleStatus;
use Illuminate\Validation\Rule;

class UpdateScheduleStatusRequest extends ApiFormRequest
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
            'status' => ['required', 'string', Rule::in(ScheduleStatus::values())],
        ];
    }
}
