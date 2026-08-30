<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Enums\ProspectStatus;
use App\Http\Controllers\Controller;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use App\Support\DayDateFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProspectController extends Controller
{
    public function index(Request $request): View
    {
        $prospects = Prospect::query()
            ->with(['marketingProfile.user', 'member'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('business', 'like', $search)
                        ->orWhere('resort', 'like', $search);
                });
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->toString()))
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

        return view('admin.prospects.index', [
            'prospects' => $prospects,
            'statuses' => ProspectStatus::options(),
            'days' => DayName::operationalOptions(),
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'resorts' => Prospect::query()->distinct()->orderBy('resort')->pluck('resort'),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'resort' => $request->string('resort')->toString(),
                'marketing_id' => $request->integer('marketing_id') ?: null,
                'day' => $request->string('day')->toString(),
            ],
        ]);
    }

    public function show(Prospect $prospect): View
    {
        $prospect->load(['marketingProfile.user', 'member', 'visitReports' => fn ($query) => $query->latest('visit_date')->latest('visit_time')]);

        return view('admin.prospects.show', [
            'prospect' => $prospect,
        ]);
    }
}
