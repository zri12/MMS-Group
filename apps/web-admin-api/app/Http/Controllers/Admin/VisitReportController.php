<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use App\Enums\VisitResult;
use App\Http\Controllers\Controller;
use App\Models\MarketingProfile;
use App\Models\VisitReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = VisitReport::query()
            ->with(['prospect', 'marketingProfile.user'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('visit_purpose', 'like', $search)
                        ->orWhere('resort', 'like', $search)
                        ->orWhereHas('prospect', fn (Builder $query) => $query->where('name', 'like', $search)->orWhere('phone', 'like', $search))
                        ->orWhereHas('marketingProfile', fn (Builder $query) => $query->where('code', 'like', $search))
                        ->orWhereHas('marketingProfile.user', fn (Builder $query) => $query->where('name', 'like', $search));
                });
            })
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('visit_date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('visit_date', '<=', $request->date('date_to')))
            ->when($request->filled('result'), fn (Builder $query) => $query->where('visit_result', $request->string('result')->toString()))
            ->when($request->filled('prospect_status'), fn (Builder $query) => $query->where('prospect_status', $request->string('prospect_status')->toString()))
            ->when($request->filled('resort'), fn (Builder $query) => $query->where('resort', $request->string('resort')->toString()))
            ->when($request->filled('marketing_id'), fn (Builder $query) => $query->where('marketing_profile_id', $request->integer('marketing_id')))
            ->when($request->filled('sync_status'), fn (Builder $query) => $query->where('sync_status', $request->string('sync_status')->toString()))
            ->latest('visit_date')
            ->latest('visit_time')
            ->paginate(15)
            ->withQueryString();

        return view('admin.visit-reports.index', [
            'reports' => $reports,
            'results' => VisitResult::options(),
            'prospectStatuses' => ProspectStatus::options(),
            'syncStatuses' => SyncStatus::options(),
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'resorts' => VisitReport::query()->distinct()->orderBy('resort')->pluck('resort'),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'date_from' => $request->string('date_from')->toString(),
                'date_to' => $request->string('date_to')->toString(),
                'result' => $request->string('result')->toString(),
                'prospect_status' => $request->string('prospect_status')->toString(),
                'resort' => $request->string('resort')->toString(),
                'marketing_id' => $request->integer('marketing_id') ?: null,
                'sync_status' => $request->string('sync_status')->toString(),
            ],
        ]);
    }

    public function show(VisitReport $visitReport): View
    {
        $visitReport->load(['prospect', 'marketingProfile.user']);

        return view('admin.visit-reports.show', [
            'report' => $visitReport,
        ]);
    }
}
