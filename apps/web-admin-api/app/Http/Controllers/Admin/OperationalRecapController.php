<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\GenerateOperationalRecapAction;
use App\Enums\DayName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenerateOperationalRecapRequest;
use App\Models\OperationalRecap;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
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
            'days' => DayName::operationalOptions(),
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

    public function generate(GenerateOperationalRecapRequest $request, GenerateOperationalRecapAction $action): RedirectResponse
    {
        $recap = $action->execute(
            CarbonImmutable::createFromFormat('!Y-m-d', $request->validated('recap_date')),
            $request->user(),
        );

        return redirect()
            ->route('admin.operational-recaps.show', $recap)
            ->with('flash_message', 'Rekap operasional berhasil diperbarui.');
    }
}
