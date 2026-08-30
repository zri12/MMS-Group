<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateMarketingAction;
use App\Actions\Admin\ResetMarketingPasswordAction;
use App\Actions\Admin\UpdateMarketingAction;
use App\Actions\Admin\UpdateMarketingStatusAction;
use App\Enums\DayName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetMarketingPasswordRequest;
use App\Http\Requests\Admin\StoreMarketingRequest;
use App\Http\Requests\Admin\UpdateMarketingRequest;
use App\Http\Requests\Admin\UpdateMarketingStatusRequest;
use App\Models\MarketingProfile;
use App\Models\User;
use App\Support\ProfilePhoto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function index(Request $request): View
    {
        $profiles = MarketingProfile::query()
            ->with(['user', 'workDays', 'latestTrackingSession'])
            ->withCount(['prospects', 'members', 'schedules'])
            ->withSum('dailyOperationalReports as total_target_sum', 'total_target_amount')
            ->withSum('dailyOperationalReports as total_drop_sum', 'drop_amount')
            ->withSum('dailyOperationalReports as total_storting_sum', 'storting')
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('code', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('area', 'like', $search)
                        ->orWhereHas('user', function (Builder $query) use ($search): void {
                            $query
                                ->where('name', 'like', $search)
                                ->orWhere('username', 'like', $search)
                                ->orWhere('email', 'like', $search);
                        });
                });
            })
            ->when($request->filled('status'), function (Builder $query) use ($request): void {
                $query->whereHas('user', fn (Builder $query) => $query->where('is_active', $request->string('status')->toString() === 'active'));
            })
            ->when($request->filled('area'), fn (Builder $query) => $query->where('area', $request->string('area')->toString()))
            ->when($request->filled('day'), function (Builder $query) use ($request): void {
                $query->whereHas('workDays', fn (Builder $query) => $query->where('day_name', $request->string('day')->toString()));
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        $profiles->getCollection()->each(fn (MarketingProfile $profile) => $this->attachDisplayIdentity($profile));

        return view('admin.marketing.index', [
            'profiles' => $profiles,
            'days' => DayName::operationalOptions(),
            'areas' => MarketingProfile::query()->distinct()->orderBy('area')->pluck('area'),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'area' => $request->string('area')->toString(),
                'day' => $request->string('day')->toString(),
            ],
            'totalMarketing' => MarketingProfile::query()->count(),
            'activeMarketing' => MarketingProfile::query()->whereHas('user', fn (Builder $query) => $query->where('is_active', true))->count(),
            'photoResolver' => fn (MarketingProfile $profile): string => ProfilePhoto::marketing($profile),
        ]);
    }

    public function create(): View
    {
        return view('admin.marketing.create', [
            'days' => DayName::operationalOptions(),
        ]);
    }

    public function store(StoreMarketingRequest $request, CreateMarketingAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['profile_photo'] = $request->file('profile_photo');

        $marketing = $action->execute($data);

        return redirect()
            ->route('admin.marketing.show', $marketing)
            ->with('flash_message', 'PDL berhasil ditambahkan.');
    }

    public function show(MarketingProfile $marketing): View
    {
        $marketing->load([
            'user',
            'workDays',
            'latestTrackingSession',
            'latestTrackingSession.latestPoint',
        ]);
        $this->attachDisplayIdentity($marketing);

        $scheduleItems = $marketing->schedules()
            ->with('prospect')
            ->latest('schedule_date')
            ->latest('start_time')
            ->limit(8)
            ->get();

        $trackingItems = $marketing->trackingSessions()
            ->with(['latestPoint', 'schedule'])
            ->latest('started_at')
            ->limit(6)
            ->get();

        $prospectItems = $marketing->prospects()
            ->latest('input_date')
            ->latest('input_time')
            ->limit(6)
            ->get();

        $memberItems = $marketing->members()
            ->latest('input_date')
            ->latest('input_time')
            ->limit(6)
            ->get();

        $operationalItems = $marketing->dailyOperationalReports()
            ->latest('report_date')
            ->latest('report_time')
            ->limit(6)
            ->get();

        $visitItems = $marketing->visitReports()
            ->with('prospect')
            ->latest('visit_date')
            ->latest('visit_time')
            ->limit(6)
            ->get();

        $journeyItems = $marketing->trackingSessions()
            ->with(['latestPoint', 'schedule'])
            ->latest('session_date')
            ->latest('started_at')
            ->limit(6)
            ->get();

        $operationalTotals = $marketing->dailyOperationalReports()
            ->selectRaw('COALESCE(SUM(total_target_amount), 0) as total_target')
            ->selectRaw('COALESCE(SUM(drop_amount), 0) as total_drop')
            ->selectRaw('COALESCE(SUM(storting), 0) as total_storting')
            ->first();

        return view('admin.marketing.show', [
            'marketing' => $marketing,
            'photoUrl' => ProfilePhoto::marketing($marketing),
            'tabs' => [
                'summary' => 'Ringkasan',
                'schedules' => 'Jadwal',
                'tracking' => 'Tracking',
                'prospects' => 'Prospek',
                'members' => 'Anggota',
                'operational' => 'Laporan Operasional',
                'visits' => 'Kunjungan',
                'journeys' => 'Riwayat',
            ],
            'tabData' => [
                'schedules' => $scheduleItems,
                'tracking' => $trackingItems,
                'prospects' => $prospectItems,
                'members' => $memberItems,
                'operational' => $operationalItems,
                'visits' => $visitItems,
                'journeys' => $journeyItems,
            ],
            'summary' => [
                'prospects' => $marketing->prospects()->count(),
                'members' => $marketing->members()->count(),
                'schedules' => $marketing->schedules()->count(),
                'visits' => $marketing->visitReports()->count(),
                'tracking_sessions' => $marketing->trackingSessions()->count(),
                'total_target' => (int) $operationalTotals->total_target,
                'total_drop' => (int) $operationalTotals->total_drop,
                'total_storting' => (int) $operationalTotals->total_storting,
            ],
        ]);
    }

    public function edit(MarketingProfile $marketing): View
    {
        $marketing->load(['user', 'workDays']);

        return view('admin.marketing.edit', [
            'marketing' => $marketing,
            'days' => DayName::operationalOptions(),
        ]);
    }

    public function update(UpdateMarketingRequest $request, MarketingProfile $marketing, UpdateMarketingAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['profile_photo'] = $request->file('profile_photo');

        $action->execute($marketing->load('user'), $data);

        return redirect()
            ->route('admin.marketing.show', $marketing)
            ->with('flash_message', 'PDL berhasil diperbarui.');
    }

    public function status(UpdateMarketingStatusRequest $request, MarketingProfile $marketing, UpdateMarketingStatusAction $action): RedirectResponse
    {
        $action->execute($marketing->load('user'), $request->boolean('is_active'));

        return back()->with('flash_message', $request->boolean('is_active')
            ? 'Akun marketing berhasil diaktifkan.'
            : 'Akun marketing berhasil dinonaktifkan.');
    }

    public function resetPassword(ResetMarketingPasswordRequest $request, MarketingProfile $marketing, ResetMarketingPasswordAction $action): RedirectResponse
    {
        $action->execute($marketing->load('user'), (string) $request->validated('password'));

        return back()->with('flash_message', 'Password marketing berhasil diperbarui.');
    }

    private function attachDisplayIdentity(MarketingProfile $profile): void
    {
        if ($profile->user || ! $profile->display_name) {
            return;
        }

        $profile->setRelation('user', new User([
            'name' => $profile->display_name,
            'username' => 'Tidak tersedia',
            'is_active' => false,
        ]));
    }
}
