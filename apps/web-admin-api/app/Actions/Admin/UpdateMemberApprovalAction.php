<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Enums\MemberApprovalStatus;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateMemberApprovalAction
{
    public function execute(Member $member, MemberApprovalStatus $status, User $actor, ?string $reason = null): Member
    {
        return DB::transaction(function () use ($member, $status, $actor, $reason): Member {
            $locked = Member::query()
                ->whereKey($member->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->approval_status !== MemberApprovalStatus::Pending) {
                throw ValidationException::withMessages([
                    'approval_status' => 'Status final tidak dapat diubah pada tahap ini.',
                ]);
            }

            if ($status === MemberApprovalStatus::Approved) {
                $locked->forceFill([
                    'approval_status' => $status,
                    'approved_by' => $actor->id,
                    'approved_at' => now(),
                    'rejected_by' => null,
                    'rejected_at' => null,
                    'rejection_reason' => null,
                ])->save();
            }

            if ($status === MemberApprovalStatus::Rejected) {
                $locked->forceFill([
                    'approval_status' => $status,
                    'approved_by' => null,
                    'approved_at' => null,
                    'rejected_by' => $actor->id,
                    'rejected_at' => now(),
                    'rejection_reason' => $reason,
                ])->save();
            }

            return $locked->refresh()->load(['marketingProfile.user', 'sourceProspect', 'approvedBy', 'rejectedBy']);
        });
    }
}
