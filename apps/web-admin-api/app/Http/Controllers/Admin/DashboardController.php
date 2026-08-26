<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Enums\OperationalAttachmentType;
use App\Enums\TrackingStatus;
use App\Http\Controllers\Controller;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\OperationalReportAttachment;
use App\Models\TrackingSession;
use App\Support\CompactCurrency;
use App\Support\ProfilePhoto;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $selectedDate = CarbonImmutable::parse($request->string('date')->toString() ?: now(config('app.timezone'))->toDateString());
        $selectedDay = DayName::tryFrom($request->string('day')->toString()) ?? $this->dayFromDate($selectedDate);
        $date = $selectedDate->toDateString();

        $reportTotals = DailyOperationalReport::query()
            ->whereDate('report_date', $date)
            ->where('day_name', $selectedDay->value)
            ->selectRaw('COALESCE(SUM(drop_amount), 0) as drop_total')
            ->selectRaw('COALESCE(SUM(storting), 0) as storting_total')
            ->selectRaw('COALESCE(SUM(incoming_target_amount), 0) as target_incoming_total')
            ->selectRaw('COALESCE(SUM(outgoing_target_amount), 0) as target_outgoing_total')
            ->selectRaw('COALESCE(SUM(total_target_amount), 0) as target_total')
            ->firstOrFail();

        $circulationTotal = (int) OperationalRecap::query()
            ->whereDate('recap_date', $date)
            ->where('day_name', $selectedDay->value)
            ->join('operational_recap_rows', 'operational_recap_rows.operational_recap_id', '=', 'operational_recaps.id')
            ->sum('operational_recap_rows.current_circulation');

        $reportPeriod = function ($query) use ($date, $selectedDay): void {
            $query->whereDate('report_date', $date)
                ->where('day_name', $selectedDay->value);
        };

        $pdlProfiles = MarketingProfile::query()
            ->with('user')
            ->withSum(['dailyOperationalReports as target_total' => $reportPeriod], 'total_target_amount')
            ->withSum(['dailyOperationalReports as drop_total' => $reportPeriod], 'drop_amount')
            ->withSum(['dailyOperationalReports as storting_total' => $reportPeriod], 'storting')
            ->orderBy('code')
            ->get()
            ->map(fn (MarketingProfile $profile): array => [
                'code' => $profile->code,
                'name' => $profile->user?->name ?? $profile->display_name ?? $profile->code,
                'area' => $profile->area,
                'photo_url' => ProfilePhoto::marketing($profile),
                'is_active' => (bool) $profile->user?->is_active,
                'target_total' => (int) $profile->target_total,
                'drop_total' => (int) $profile->drop_total,
                'storting_total' => (int) $profile->storting_total,
                'detail_url' => route('admin.marketing.show', $profile),
            ]);

        $attachmentPanels = collect(OperationalAttachmentType::cases())->mapWithKeys(function (OperationalAttachmentType $type) use ($date, $selectedDay): array {
            $attachments = OperationalReportAttachment::query()
                ->with(['dailyOperationalReport.marketingProfile.user'])
                ->where('type', $type->value)
                ->whereHas('dailyOperationalReport', fn ($query) => $query
                    ->whereDate('report_date', $date)
                    ->where('day_name', $selectedDay->value))
                ->latest('uploaded_at')
                ->latest('id')
                ->limit(3)
                ->get()
                ->map(fn (OperationalReportAttachment $attachment): array => [
                    'url' => Storage::url($attachment->photo_path),
                    'report_url' => route('admin.operational-reports.show', $attachment->daily_operational_report_id),
                    'caption' => $attachment->caption,
                    'uploaded_at' => $attachment->uploaded_at,
                    'pdl_name' => $attachment->dailyOperationalReport->marketingProfile->user?->name ?? $attachment->dailyOperationalReport->marketingProfile->display_name ?? 'PDL historis',
                    'pdl_code' => $attachment->dailyOperationalReport->marketingProfile->code,
                ]);

            return [$type->value => $attachments];
        });

        return view('admin.dashboard', [
            'selectedDate' => $selectedDate,
            'selectedDay' => $selectedDay,
            'metricCards' => [
                ['label' => 'Drop', 'value' => (int) $reportTotals->drop_total, 'type' => 'currency', 'icon' => 'arrow-down'],
                ['label' => 'Storting', 'value' => (int) $reportTotals->storting_total, 'type' => 'currency', 'icon' => 'wallet'],
                ['label' => 'Sirkulasi', 'value' => $circulationTotal, 'type' => 'currency', 'icon' => 'refresh'],
                ['label' => 'Target Masuk', 'value' => (int) $reportTotals->target_incoming_total, 'type' => 'currency', 'icon' => 'arrow-in'],
                ['label' => 'Target Keluar', 'value' => (int) $reportTotals->target_outgoing_total, 'type' => 'currency', 'icon' => 'arrow-out'],
                ['label' => 'Total Target', 'value' => (int) $reportTotals->target_total, 'type' => 'currency', 'icon' => 'target'],
                ['label' => 'Anggota Masuk', 'value' => null, 'type' => 'count', 'icon' => 'member-in'],
                ['label' => 'Anggota Keluar', 'value' => null, 'type' => 'count', 'icon' => 'member-out'],
                ['label' => 'Total Anggota', 'value' => Member::query()->count(), 'type' => 'count', 'icon' => 'members'],
            ],
            'compactCurrency' => fn (int $amount): string => CompactCurrency::format($amount),
            'attachmentPanels' => $attachmentPanels,
            'pdlProfiles' => $pdlProfiles,
            'activeTrackingCount' => TrackingSession::query()
                ->whereDate('session_date', $date)
                ->where('status', TrackingStatus::Active->value)
                ->count(),
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
}
