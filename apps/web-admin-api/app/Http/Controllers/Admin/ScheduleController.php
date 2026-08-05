<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Enums\ScheduleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScheduleRequest;
use App\Http\Requests\Admin\UpdateScheduleRequest;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = MarketingSchedule::query()
            ->with(['marketingProfile.user', 'prospect'])
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('agenda', 'like', $search)
                        ->orWhere('consumer_name_snapshot', 'like', $search)
                        ->orWhere('area', 'like', $search)
                        ->orWhere('resort', 'like', $search)
                        ->orWhereHas('marketingProfile.user', fn (Builder $query) => $query->where('name', 'like', $search))
                        ->orWhereHas('marketingProfile', fn (Builder $query) => $query->where('code', 'like', $search));
                });
            })
            ->when($request->filled('date'), fn (Builder $query) => $query->whereDate('schedule_date', $request->date('date')))
            ->when($request->filled('day'), fn (Builder $query) => $query->where('day_name', $request->string('day')->toString()))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('marketing_id'), fn (Builder $query) => $query->where('marketing_profile_id', $request->integer('marketing_id')))
            ->orderBy('schedule_date')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        return view('admin.schedules.index', [
            'schedules' => $schedules,
            'days' => DayName::options(),
            'statuses' => ScheduleStatus::options(),
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'date' => $request->string('date')->toString(),
                'day' => $request->string('day')->toString(),
                'status' => $request->string('status')->toString(),
                'marketing_id' => $request->integer('marketing_id') ?: null,
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.schedules.create', $this->formData());
    }

    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $schedule = MarketingSchedule::query()->create($data);

        return redirect()
            ->route('admin.schedules.index')
            ->with('flash_message', 'Jadwal marketing berhasil ditambahkan.');
    }

    public function edit(MarketingSchedule $schedule): View
    {
        return view('admin.schedules.edit', $this->formData() + [
            'schedule' => $schedule->load(['marketingProfile.user', 'prospect']),
        ]);
    }

    public function update(UpdateScheduleRequest $request, MarketingSchedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        return redirect()
            ->route('admin.schedules.index')
            ->with('flash_message', 'Jadwal marketing berhasil diperbarui.');
    }

    public function destroy(MarketingSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()
            ->route('admin.schedules.index')
            ->with('flash_message', 'Jadwal marketing berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'days' => DayName::options(),
            'statuses' => ScheduleStatus::options(),
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'prospects' => Prospect::query()->with('marketingProfile.user')->orderBy('name')->get(),
        ];
    }
}
