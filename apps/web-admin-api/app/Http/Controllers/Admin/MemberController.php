<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\UpdateMemberApprovalAction;
use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateMemberApprovalRequest;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Support\DayDateFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::query()
            ->with(['marketingProfile.user', 'sourceProspect'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('member_number', 'like', $search)
                        ->orWhere('loan_number', 'like', $search);
                });
            })
            ->when($request->filled('approval_status'), fn (Builder $query) => $query->where('approval_status', $request->string('approval_status')->toString()))
            ->when($request->filled('resort'), fn (Builder $query) => $query->where('resort', $request->string('resort')->toString()))
            ->when($request->filled('marketing_id'), fn (Builder $query) => $query->where('marketing_profile_id', $request->integer('marketing_id')))
            ->when($request->filled('day'), function (Builder $query) use ($request): void {
                $day = DayName::tryFrom($request->string('day')->toString());

                if ($day) {
                    DayDateFilter::apply($query, 'input_date', $day);
                }
            })
            ->latest('input_date')
            ->latest('input_time')
            ->paginate(15)
            ->withQueryString();

        return view('admin.members.index', [
            'members' => $members,
            'statuses' => MemberApprovalStatus::options(),
            'days' => DayName::options(),
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'resorts' => Member::query()->distinct()->orderBy('resort')->pluck('resort'),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'approval_status' => $request->string('approval_status')->toString(),
                'resort' => $request->string('resort')->toString(),
                'marketing_id' => $request->integer('marketing_id') ?: null,
                'day' => $request->string('day')->toString(),
            ],
        ]);
    }

    public function show(Member $member): View
    {
        $member->load(['marketingProfile.user', 'sourceProspect', 'approvedBy', 'rejectedBy']);

        return view('admin.members.show', [
            'member' => $member,
        ]);
    }

    public function approval(UpdateMemberApprovalRequest $request, Member $member, UpdateMemberApprovalAction $action): RedirectResponse
    {
        $action->execute(
            member: $member,
            status: MemberApprovalStatus::from($request->validated('approval_status')),
            actor: $request->user(),
            reason: $request->validated('rejection_reason'),
        );

        return back()->with('flash_message', 'Status anggota berhasil diperbarui.');
    }
}
