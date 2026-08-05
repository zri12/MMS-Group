<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Models\Member;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends ApiFormRequest
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
        $existingMemberId = $this->existingMemberIdByLocalUuid();

        return [
            'local_uuid' => ['required', 'uuid'],
            'source_prospect_id' => ['nullable', 'integer', 'exists:prospects,id', Rule::unique('members', 'source_prospect_id')->ignore($existingMemberId)],
            'resort' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i:s'],
            'name' => ['required', 'string', 'max:150'],
            'member_number' => ['required', 'string', 'max:50', Rule::unique('members', 'member_number')->ignore($existingMemberId)],
            'loan_number' => ['required', 'string', 'max:50', Rule::unique('members', 'loan_number')->ignore($existingMemberId)],
            'address' => ['required', 'string', 'max:5000'],
            'phone' => ['required', 'string', 'max:30'],
            'business' => ['required', 'string', 'max:150'],
            'loan_amount' => ['required', 'integer', 'min:0'],
            'installment_amount' => ['required', 'integer', 'min:0'],
            'insurance_amount' => ['required', 'integer', 'min:0'],
            'collateral' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_address' => ['nullable', 'string', 'max:255'],
            'member_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('mms.uploads.max_image_kb', 2048)],
        ];
    }

    private function existingMemberIdByLocalUuid(): ?int
    {
        $localUuid = $this->input('local_uuid');

        if (! is_string($localUuid)) {
            return null;
        }

        return Member::query()
            ->where('local_uuid', $localUuid)
            ->value('id');
    }
}
