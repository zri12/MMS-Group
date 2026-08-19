<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Enums\OperationalAttachmentType;
use App\Enums\SyncStatus;
use App\Http\Controllers\Controller;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OperationalReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = DailyOperationalReport::query()
            ->with('marketingProfile.user')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('resort', 'like', $search)
                        ->orWhere('notes', 'like', $search)
                        ->orWhereHas('marketingProfile', fn (Builder $query) => $query->where('code', 'like', $search))
                        ->orWhereHas('marketingProfile.user', fn (Builder $query) => $query->where('name', 'like', $search));
                });
            })
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('report_date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('report_date', '<=', $request->date('date_to')))
            ->when($request->filled('day'), fn (Builder $query) => $query->where('day_name', $request->string('day')->toString()))
            ->when($request->filled('resort'), fn (Builder $query) => $query->where('resort', $request->string('resort')->toString()))
            ->when($request->filled('marketing_id'), fn (Builder $query) => $query->where('marketing_profile_id', $request->integer('marketing_id')))
            ->when($request->filled('sync_status'), fn (Builder $query) => $query->where('sync_status', $request->string('sync_status')->toString()))
            ->latest('report_date')
            ->latest('report_time')
            ->paginate(15)
            ->withQueryString();

        return view('admin.operational-reports.index', [
            'reports' => $reports,
            'days' => DayName::options(),
            'syncStatuses' => SyncStatus::options(),
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'resorts' => DailyOperationalReport::query()->distinct()->orderBy('resort')->pluck('resort'),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'date_from' => $request->string('date_from')->toString(),
                'date_to' => $request->string('date_to')->toString(),
                'day' => $request->string('day')->toString(),
                'resort' => $request->string('resort')->toString(),
                'marketing_id' => $request->integer('marketing_id') ?: null,
                'sync_status' => $request->string('sync_status')->toString(),
            ],
        ]);
    }

    public function show(DailyOperationalReport $operationalReport): View
    {
        $operationalReport->load(['marketingProfile.user', 'attachments']);

        $attachmentsByType = $operationalReport->attachments->keyBy(
            fn ($attachment): string => $attachment->type->value,
        );
        $attachmentPanels = [];

        foreach (OperationalAttachmentType::cases() as $type) {
            $attachment = $attachmentsByType->get($type->value);

            $attachmentPanels[$type->value] = [
                'label' => $type->label(),
                'url' => $attachment ? Storage::url($attachment->photo_path) : null,
                'caption' => $attachment?->caption,
            ];
        }

        return view('admin.operational-reports.show', [
            'report' => $operationalReport,
            'attachmentPanels' => $attachmentPanels,
        ]);
    }
}
