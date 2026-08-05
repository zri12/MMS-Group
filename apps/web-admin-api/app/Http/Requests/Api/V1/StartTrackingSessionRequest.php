<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

class StartTrackingSessionRequest extends ApiFormRequest
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
            'schedule_id' => ['nullable', 'integer', 'exists:marketing_schedules,id'],
            'started_at' => ['required', 'date'],
        ];
    }
}
