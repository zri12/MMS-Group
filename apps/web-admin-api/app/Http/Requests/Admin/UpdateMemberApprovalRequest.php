<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\MemberApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true && $this->user()?->is_active === true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'approval_status' => ['required', 'string', Rule::in([
                MemberApprovalStatus::Approved->value,
                MemberApprovalStatus::Rejected->value,
            ])],
            'rejection_reason' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
