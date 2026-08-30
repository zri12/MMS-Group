<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Http\Controllers\Controller;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\Prospect;
use App\Models\TrackingSession;
use App\Models\VisitReport;
use App\Support\ProfilePhoto;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyController extends Controller
{
    public function __invoke(Request $request): View
    {
        $selectedDay = DayName::tryFrom($request->string('day')->toString()) ?? DayName::Monday;
        $date = $this->dateForDay($request->string('date')->toString(), $selectedDay);

        // ── Counters ──────────────────────────────────────────────────────
        $counters = [
            'prospects' => Prospect::query()->whereDate('input_date', $date->toDateString())->count(),
            'members' => Member::query()->whereDate('input_date', $date->toDateString())->count(),
            'operational_reports' => DailyOperationalReport::query()->whereDate('report_date', $date->toDateString())->count(),
            'visit_reports' => VisitReport::query()->whereDate('visit_date', $date->toDateString())->count(),
            'schedules' => MarketingSchedule::query()->whereDate('schedule_date', $date->toDateString())->count(),
            'tracking_sessions' => TrackingSession::query()->whereDate('session_date', $date->toDateString())->count(),
            'recaps' => OperationalRecap::query()->whereDate('recap_date', $date->toDateString())->count(),
            'active_tracking' => TrackingSession::query()->whereDate('session_date', $date->toDateString())->where('status', 'Aktif')->count(),
        ];

        // ── Jadwal hari ini (compact) ─────────────────────────────────────
        $scheduleToday = MarketingSchedule::query()
            ->with(['marketingProfile.user', 'prospect'])
            ->whereDate('schedule_date', $date->toDateString())
            ->where('day_name', $selectedDay->value)
            ->orderBy('start_time')
            ->limit(8)
            ->get();

        // ── Marketing yang aktif tracking hari ini ────────────────────────
        $activeTrackingList = TrackingSession::query()
            ->with(['marketingProfile.user'])
            ->whereDate('session_date', $date->toDateString())
            ->where('status', 'Aktif')
            ->limit(5)
            ->get();

        $photoResolver = fn (MarketingProfile $p): string => ProfilePhoto::marketing($p);

        return view('admin.daily.index', [
            'days' => DayName::operationalOptions(),
            'selectedDay' => $selectedDay,
            'selectedDate' => $date,
            'filters' => [
                'day' => $selectedDay->value,
                'date' => $date->toDateString(),
            ],
            'counters' => $counters,
            'scheduleToday' => $scheduleToday,
            'activeTrackingList' => $activeTrackingList,
            'photoResolver' => $photoResolver,
        ]);
    }

    private function dateForDay(string $date, DayName $day): CarbonImmutable
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1) {
            return CarbonImmutable::createFromFormat('!Y-m-d', $date);
        }

        $today = CarbonImmutable::now(config('mms.timezone'))->startOfDay();

        return match ($day) {
            DayName::Monday => $today->startOfWeek(),
            DayName::Tuesday => $today->startOfWeek()->addDay(),
            DayName::Wednesday => $today->startOfWeek()->addDays(2),
            DayName::Thursday => $today->startOfWeek()->addDays(3),
            DayName::Friday => $today->startOfWeek()->addDays(4),
            DayName::Saturday => $today->startOfWeek()->addDays(5),
            DayName::Sunday => $today->startOfWeek()->addDays(6),
        };
    }
}
