<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\DayName;
use App\Enums\OperationalAttachmentType;
use Illuminate\Validation\Rule;

class StoreOperationalReportRequest extends ApiFormRequest
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
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i:s'],
            'day' => ['required', 'string', Rule::in(DayName::values())],
            'resort' => ['required', 'string', 'max:100'],
            'storting' => ['required', 'integer', 'min:0'],
            'insurance_amount' => ['required', 'integer', 'min:0'],
            'drop' => ['required', 'integer', 'min:0'],
            'withdrawal_saving' => ['required', 'integer', 'min:0'],
            'previous_target_amount' => ['required', 'integer', 'min:0'],
            'previous_target_people' => ['required', 'integer', 'min:0'],
            'incoming_target_amount' => ['required', 'integer', 'min:0'],
            'incoming_target_people' => ['required', 'integer', 'min:0'],
            'outgoing_target_amount' => ['required', 'integer', 'min:0'],
            'outgoing_target_people' => ['required', 'integer', 'min:0'],
            'total_target_amount' => ['required', 'integer', 'min:0'],
            'total_target_people' => ['required', 'integer', 'min:0'],
            'new_drop' => ['required', 'integer', 'min:0'],
            'continued_drop' => ['required', 'integer', 'min:0'],
            'incoming_member_count' => ['prohibited'],
            'outgoing_member_count' => ['prohibited'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:2'],
            'attachments.*.type' => ['required_with:attachments', 'string', 'distinct:strict', Rule::in(array_map(fn (OperationalAttachmentType $type) => $type->value, OperationalAttachmentType::cases()))],
            'attachments.*.photo' => ['required_with:attachments', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('mms.uploads.max_image_kb')],
            'attachments.*.caption' => ['nullable', 'string', 'max:255'],
        ];
    }
}
