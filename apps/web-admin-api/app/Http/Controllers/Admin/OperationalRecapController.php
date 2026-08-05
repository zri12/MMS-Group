<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Http\Controllers\Controller;
use App\Models\OperationalRecap;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperationalRecapController extends Controller
{
    public function index(Request $request): View
    {
        $recaps = OperationalRecap::query()
            ->withCount('rows')
            ->when($request->filled('day'), fn (Builder $query) => $query->where('day_name', $request->string('day')->toString()))
            ->when($request->filled('date'), fn (Builder $query) => $query->whereDate('recap_date', $request->date('date')))
            ->latest('recap_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.operational-recaps.index', [
            'recaps' => $recaps,
            'days' => DayName::options(),
            'filters' => [
                'day' => $request->string('day')->toString(),
                'date' => $request->string('date')->toString(),
            ],
        ]);
    }

    public function show(OperationalRecap $operationalRecap): View
    {
        $operationalRecap->load(['rows.marketingProfile.user']);

        return view('admin.operational-recaps.show', [
            'recap' => $operationalRecap,
        ]);
    }
}
