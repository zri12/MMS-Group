<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use App\Enums\TrackingStatus;
use App\Http\Controllers\Controller;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\Prospect;
use App\Models\VisitReport;
use App\Support\ProfilePhoto;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $selectedDate = CarbonImmutable::parse($request->string('date')->toString() ?: now(config('app.timezone'))->toDateString());
        $selectedDay = DayName::tryFrom($request->string('day')->toString()) ?? $this->dayFromDate($selectedDate);
        $selectedMarketingId = $request->integer('marketing_id') ?: null;

        // ── Activity queries (filter-based) ──────────────────────────────
        $memberQuery = Member::query()
            ->whereDate('input_date', $selectedDate->toDateString())
            ->when($selectedMarketingId, fn (Builder $q) => $q->where('marketing_profile_id', $selectedMarketingId));

        $prospectQuery = Prospect::query()
            ->whereDate('input_date', $selectedDate->toDateString())
            ->when($selectedMarketingId, fn (Builder $q) => $q->where('marketing_profile_id', $selectedMarketingId));

        $visitQuery = VisitReport::query()
            ->whereDate('visit_date', $selectedDate->toDateString())
            ->where('day_name', $selectedDay->value)
            ->when($selectedMarketingId, fn (Builder $q) => $q->where('marketing_profile_id', $selectedMarketingId));

        $reportQuery = DailyOperationalReport::query()
            ->whereDate('report_date', $selectedDate->toDateString())
            ->where('day_name', $selectedDay->value)
            ->when($selectedMarketingId, fn (Builder $q) => $q->where('marketing_profile_id', $selectedMarketingId));

        $reportTotals = (clone $reportQuery)
            ->selectRaw('COALESCE(SUM(total_target_amount), 0) as total_target')
            ->selectRaw('COALESCE(SUM(drop_amount), 0) as total_drop')
            ->selectRaw('COALESCE(SUM(storting), 0) as total_storting')
            ->first();

        $scheduleQuery = MarketingSchedule::query()
            ->with(['marketingProfile.user', 'prospect'])
            ->whereDate('schedule_date', $selectedDate->toDateString())
            ->where('day_name', $selectedDay->value)
            ->when($selectedMarketingId, fn (Builder $q) => $q->where('marketing_profile_id', $selectedMarketingId));

        // Global report totals (when no marketing filter selected)
        $dateReportTotals = DailyOperationalReport::query()
            ->whereDate('report_date', $selectedDate->toDateString())
            ->where('day_name', $selectedDay->value)
            ->selectRaw('COALESCE(SUM(total_target_amount), 0) as total_target')
            ->selectRaw('COALESCE(SUM(drop_amount), 0) as total_drop')
            ->selectRaw('COALESCE(SUM(storting), 0) as total_storting')
            ->first();

        // ── Global totals (tidak dipengaruhi filter tanggal) ─────────────
        $globalTotals = Member::query()
            ->selectRaw('COUNT(*) as total_members')
            ->selectRaw('SUM(CASE WHEN approval_status = ? THEN 1 ELSE 0 END) as approved_members', [MemberApprovalStatus::Approved->value])
            ->first();

        $effectiveReportTotals = $selectedMarketingId ? $reportTotals : $dateReportTotals;

        // ── Rekap harian ─────────────────────────────────────────────────
        $recap = OperationalRecap::query()
            ->with(['rows.marketingProfile.user'])
            ->whereDate('recap_date', $selectedDate->toDateString())
            ->where('day_name', $selectedDay->value)
            ->first();

        // ── Marketing carousel: semua marketing dengan tracking status ────
        $allMarketing = MarketingProfile::query()
            ->with(['user', 'workDays', 'latestTrackingSession'])
            ->whereHas('user', fn (Builder $q) => $q->where('is_active', true))
            ->orderBy('code')
            ->get();

        $photoResolver = fn (MarketingProfile $p): string => ProfilePhoto::marketing($p);

        $marketingCarousel = $allMarketing->map(function (MarketingProfile $marketing) use ($selectedDay, $photoResolver): array {
            $session = $marketing->latestTrackingSession;
            $trackStatus = $session?->status?->label() ?? 'Belum Mulai';
            $statusValue = $session?->status?->value ?? '';
            $lastActive = $session?->updated_at?->timezone(config('app.timezone'))?->format('H:i') ?? null;

            $worksToday = $marketing->workDays->contains(fn ($wd) => $wd->day_name->value === $selectedDay->value);

            return [
                'id' => $marketing->id,
                'code' => $marketing->code,
                'name' => $marketing->user->name,
                'area' => $marketing->area,
                'photo_url' => $photoResolver($marketing),
                'track_status' => $trackStatus,
                'status_value' => $statusValue,
                'last_active' => $lastActive,
                'works_today' => $worksToday,
                'detail_url' => route('admin.marketing.show', $marketing),
            ];
        });

        return view('admin.dashboard', [
            'user' => $request->user(),
            'days' => DayName::options(),
            'selectedDay' => $selectedDay,
            'selectedDate' => $selectedDate,
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'filters' => [
                'date' => $selectedDate->toDateString(),
                'day' => $selectedDay->value,
                'marketing_id' => $selectedMarketingId,
            ],

            // Primary summary
            'primarySummary' => [
                'total_members' => (int) $globalTotals->total_members,
                'approved_members' => (int) $globalTotals->approved_members,
                'total_target' => (int) $effectiveReportTotals->total_target,
                'total_drop' => (int) $effectiveReportTotals->total_drop,
                'total_storting' => (int) $effectiveReportTotals->total_storting,
            ],

            // Activity summary (filter-based)
            'activitySummary' => [
                'new_prospects' => (clone $prospectQuery)->where('status', ProspectStatus::New->value)->count(),
                'visits' => (clone $visitQuery)->count(),
                'pending_sync' => $this->pendingSyncCount($selectedDate, $selectedMarketingId),
            ],

            // Marketing carousel
            'marketingCarousel' => $marketingCarousel,
            'totalActiveTracking' => $allMarketing->filter(fn ($m) => ($m->latestTrackingSession?->status?->value ?? '') === TrackingStatus::Active->value)->count(),

            // Rekap mini
            'recap' => $recap,

            // Jadwal hari ini
            'scheduleToday' => (clone $scheduleQuery)->orderBy('start_time')->get(),

            // Legacy summary keys for backwards compatibility
            'summary' => [
                'members' => (clone $memberQuery)->count(),
                'approved_members' => (clone $memberQuery)->where('approval_status', MemberApprovalStatus::Approved->value)->count(),
                'total_target' => (int) $reportTotals->total_target,
                'total_drop' => (int) $reportTotals->total_drop,
                'total_storting' => (int) $reportTotals->total_storting,
                'new_prospects' => (clone $prospectQuery)->where('status', ProspectStatus::New->value)->count(),
                'visits' => (clone $visitQuery)->count(),
                'pending_sync' => $this->pendingSyncCount($selectedDate, $selectedMarketingId),
                'tracking_sessions' => 0,
            ],
            'latestReports' => (clone $reportQuery)->with('marketingProfile.user')->latest('report_date')->latest('report_time')->limit(5)->get(),
            'trackingStatuses' => $allMarketing,
            'scheduleCounts' => (clone $scheduleQuery)->select('status', DB::raw('COUNT(*) as aggregate'))->groupBy('status')->pluck('aggregate', 'status'),
        ]);
    }

    private function dayFromDate(CarbonImmutable $date): DayName
    {
        return match ($date->dayOfWeekIso) {
            1 => DayName::Monday,
            2 => DayName::Tuesday,
            3 => DayName::Wednesday,
            4 => DayName::Thursday,
            5 => DayName::Friday,
            6 => DayName::Saturday,
            default => DayName::Monday,
        };
    }

    private function pendingSyncCount(CarbonImmutable $date, ?int $marketingId): int
    {
        $filter = fn (Builder $q, string $col): Builder => $q
            ->whereDate($col, $date->toDateString())
            ->when($marketingId, fn (Builder $q2) => $q2->where('marketing_profile_id', $marketingId))
            ->where('sync_status', SyncStatus::Pending->value);

        return $filter(Prospect::query(), 'input_date')->count()
            + $filter(Member::query(), 'input_date')->count()
            + $filter(DailyOperationalReport::query(), 'report_date')->count()
            + $filter(VisitReport::query(), 'visit_date')->count();
    }
}
