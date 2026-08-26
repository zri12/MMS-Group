<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Enums\TrackingStatus;
use App\Http\Controllers\Controller;
use App\Models\MarketingProfile;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use App\Support\ProfilePhoto;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(Request $request): View
    {
        $date = $this->selectedDate($request);
        $profiles = $this->profilesForRequest($request, $date)->paginate(25)->withQueryString();

        $rows = $profiles->getCollection()
            ->map(fn (MarketingProfile $profile): array => $this->statusRow($profile, $date));

        $markers = $this->markersFromRows($rows);

        return view('admin.tracking.index', [
            'profiles' => $profiles,
            'rows' => $rows,
            'markers' => $markers,
            'days' => DayName::options(),
            'statuses' => TrackingStatus::options(),
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'pollingSeconds' => config('mms.tracking.polling_seconds', 30),
            'filters' => [
                'date' => $date->toDateString(),
                'day' => $request->string('day')->toString(),
                'marketing_id' => $request->integer('marketing_id') ?: null,
                'status' => $request->string('status')->toString(),
            ],
        ]);
    }

    public function feed(Request $request): JsonResponse
    {
        $date = $this->selectedDate($request);
        $profiles = $this->profilesForRequest($request, $date)->limit(100)->get();

        $rows = $profiles->map(fn (MarketingProfile $profile): array => $this->statusRow($profile, $date));

        return response()->json([
            'data' => [
                'markers' => $this->markersFromRows($rows)->all(),
                'updated_at' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
            ],
        ]);
    }

    public function show(TrackingSession $trackingSession): View
    {
        $trackingSession->load(['marketingProfile.user', 'schedule', 'latestPoint'])
            ->loadCount('points');

        $points = $trackingSession->points()
            ->orderBy('recorded_at')
            ->get();

        $path = $points
            ->map(fn ($point): array => [
                'lat' => (float) $point->latitude,
                'lng' => (float) $point->longitude,
                'type' => $point->point_type->label(),
                'recorded_at' => $point->recorded_at?->format('d/m/Y H:i'),
            ])
            ->values();

        $latestPoint = $points->last();
        $markers = $latestPoint ? collect([[
            'lat' => (float) $latestPoint->latitude,
            'lng' => (float) $latestPoint->longitude,
            'label' => $trackingSession->marketingProfile->code.' - '.$trackingSession->marketingProfile->user->name,
            'status' => $this->displayStatus($trackingSession, $latestPoint, $trackingSession->session_date)->label(),
            'updated_at' => $latestPoint->recorded_at?->format('d/m/Y H:i'),
            'url' => null,
            'photo_url' => ProfilePhoto::marketing($trackingSession->marketingProfile),
        ]]) : collect();

        return view('admin.tracking.show', [
            'session' => $trackingSession,
            'points' => $points,
            'markers' => $markers,
            'path' => $path,
        ]);
    }

    private function selectedDate(Request $request): CarbonImmutable
    {
        $date = $request->string('date')->toString();

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1) {
            return CarbonImmutable::createFromFormat('!Y-m-d', $date);
        }

        return CarbonImmutable::now(config('mms.timezone'))->startOfDay();
    }

    private function applyStatusFilter(Builder $query, TrackingStatus $status, CarbonImmutable $date): void
    {
        $dateString = $date->toDateString();

        if ($this->isLiveDate($date) && $status === TrackingStatus::Active) {
            $query->whereHas('trackingSessions', fn (Builder $query) => $query
                ->whereDate('session_date', $dateString)
                ->where('status', TrackingStatus::Active->value)
                ->whereHas('latestPoint', fn (Builder $pointQuery) => $pointQuery
                    ->where('received_at', '>=', $this->gpsFreshAfter())));

            return;
        }

        if ($this->isLiveDate($date) && $status === TrackingStatus::GpsInactive) {
            $query
                ->whereHas('trackingSessions', fn (Builder $query) => $query
                    ->whereDate('session_date', $dateString)
                    ->where('status', TrackingStatus::Active->value))
                ->whereDoesntHave('trackingSessions', fn (Builder $query) => $query
                    ->whereDate('session_date', $dateString)
                    ->where('status', TrackingStatus::Active->value)
                    ->whereHas('latestPoint', fn (Builder $pointQuery) => $pointQuery
                        ->where('received_at', '>=', $this->gpsFreshAfter())));

            return;
        }

        match ($status) {
            TrackingStatus::Active, TrackingStatus::Offline, TrackingStatus::GpsInactive => $query->whereHas(
                'trackingSessions',
                fn (Builder $query) => $query
                    ->whereDate('session_date', $dateString)
                    ->where('status', $status->value),
            ),
            TrackingStatus::NotStarted => $query
                ->whereHas('schedules', fn (Builder $query) => $query->whereDate('schedule_date', $dateString))
                ->whereDoesntHave('trackingSessions', fn (Builder $query) => $query->whereDate('session_date', $dateString)),
            TrackingStatus::NotScheduled => $query
                ->whereDoesntHave('schedules', fn (Builder $query) => $query->whereDate('schedule_date', $dateString))
                ->whereDoesntHave('trackingSessions', fn (Builder $query) => $query->whereDate('session_date', $dateString)),
        };
    }

    private function profilesForRequest(Request $request, CarbonImmutable $date): Builder
    {
        $status = TrackingStatus::tryFrom($request->string('status')->toString());

        return MarketingProfile::query()
            ->with([
                'user',
                'workDays',
                'schedules' => fn ($query) => $query->whereDate('schedule_date', $date->toDateString()),
                'trackingSessions' => fn ($query) => $query
                    ->whereDate('session_date', $date->toDateString())
                    ->with(['schedule', 'latestPoint'])
                    ->latest('started_at'),
            ])
            ->whereHas('user', fn (Builder $userQuery) => $userQuery->where('is_active', true))
            ->when($request->filled('marketing_id'), fn (Builder $query) => $query->whereKey($request->integer('marketing_id')))
            ->when($request->filled('day'), fn (Builder $query) => $query->whereHas('workDays', fn (Builder $query) => $query->where('day_name', $request->string('day')->toString())))
            ->when($status, fn (Builder $query) => $this->applyStatusFilter($query, $status, $date))
            ->orderBy('code');
    }

    private function markersFromRows($rows)
    {
        return $rows
            ->filter(fn (array $row): bool => $row['latitude'] !== null && $row['longitude'] !== null)
            ->map(fn (array $row): array => [
                'lat' => $row['latitude'],
                'lng' => $row['longitude'],
                'label' => $row['marketing_label'],
                'status' => $row['status_label'],
                'updated_at' => $row['last_recorded_at'],
                'url' => $row['detail_url'],
                'photo_url' => $row['photo_url'],
            ])
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function statusRow(MarketingProfile $profile, CarbonImmutable $date): array
    {
        $session = $profile->trackingSessions->first();
        $point = $session?->latestPoint;
        $status = $session
            ? $this->displayStatus($session, $point, $date)
            : ($profile->schedules->isNotEmpty() ? TrackingStatus::NotStarted : TrackingStatus::NotScheduled);

        return [
            'marketing_label' => $profile->code.' - '.$profile->user->name,
            'area' => $profile->area,
            'days' => $profile->workDays->map(fn ($day) => $day->day_name->label())->join(', '),
            'status_label' => $status->label(),
            'status_value' => $status->value,
            'photo_url' => ProfilePhoto::marketing($profile),
            'session' => $session,
            'last_recorded_at' => $point?->recorded_at?->format('d/m/Y H:i'),
            'latitude' => $point ? (float) $point->latitude : null,
            'longitude' => $point ? (float) $point->longitude : null,
            'location' => $point?->address ?? '-',
            'detail_url' => $session ? route('admin.tracking.show', $session) : null,
        ];
    }

    private function displayStatus(TrackingSession $session, ?TrackingPoint $point, CarbonInterface $date): TrackingStatus
    {
        if (
            $session->status === TrackingStatus::Active
            && $this->isLiveDate($date)
            && (! $point || ! $point->received_at || $point->received_at->lt($this->gpsFreshAfter()))
        ) {
            return TrackingStatus::GpsInactive;
        }

        return $session->status;
    }

    private function isLiveDate(CarbonInterface $date): bool
    {
        return $date->isSameDay(CarbonImmutable::now(config('mms.timezone')));
    }

    private function gpsFreshAfter(): CarbonImmutable
    {
        return CarbonImmutable::now(config('mms.timezone'))
            ->subSeconds((int) config('mms.tracking.gps_stale_seconds', 180));
    }
}
