<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

class StopTrackingSessionRequest extends ApiFormRequest
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
            'ended_at' => ['required', 'date'],
            'visit_count' => ['required', 'integer', 'min:0'],
            'distance_meters' => ['required', 'integer', 'min:0'],
        ];
    }
}
