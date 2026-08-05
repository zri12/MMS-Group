<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DayName;
use App\Enums\TrackingStatus;
use App\Http\Controllers\Controller;
use App\Models\MarketingProfile;
use App\Models\TrackingSession;
use App\Support\ProfilePhoto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JourneyController extends Controller
{
    public function index(Request $request): View
    {
        $sessions = TrackingSession::query()
            ->with(['marketingProfile.user', 'schedule', 'latestPoint'])
            ->withCount('points')
            ->when($request->filled('date'), fn (Builder $query) => $query->whereDate('session_date', $request->date('date')))
            ->when($request->filled('day'), fn (Builder $query) => $query->where('day_name', $request->string('day')->toString()))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('marketing_id'), fn (Builder $query) => $query->where('marketing_profile_id', $request->integer('marketing_id')))
            ->latest('started_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.journeys.index', [
            'sessions' => $sessions,
            'days' => DayName::options(),
            'statuses' => TrackingStatus::options(),
            'marketingOptions' => MarketingProfile::query()->with('user')->orderBy('code')->get(),
            'filters' => [
                'date' => $request->string('date')->toString(),
                'day' => $request->string('day')->toString(),
                'status' => $request->string('status')->toString(),
                'marketing_id' => $request->integer('marketing_id') ?: null,
            ],
        ]);
    }

    public function show(TrackingSession $trackingSession): View
    {
        $trackingSession->load([
            'marketingProfile.user',
            'marketingProfile.workDays',
            'schedule.prospect',
            'latestPoint',
        ])->loadCount('points');

        $points = $trackingSession->points()
            ->orderBy('recorded_at')
            ->get();

        $path = $points->map(fn ($point): array => [
            'lat' => (float) $point->latitude,
            'lng' => (float) $point->longitude,
            'type' => $point->point_type->label(),
            'recorded_at' => $point->recorded_at?->format('d/m/Y H:i'),
        ])->values();

        // Markers untuk peta rute
        $startPoint = $points->first();
        $endPoint = $points->last();

        $markers = collect();
        if ($startPoint) {
            $markers->push([
                'lat' => (float) $startPoint->latitude,
                'lng' => (float) $startPoint->longitude,
                'label' => 'Mulai',
                'type' => 'start',
                'status' => $trackingSession->status->label(),
                'updated_at' => $startPoint->recorded_at?->format('d/m/Y H:i'),
                'url' => null,
                'photo_url' => ProfilePhoto::marketing($trackingSession->marketingProfile),
            ]);
        }
        if ($endPoint && $endPoint !== $startPoint) {
            $markers->push([
                'lat' => (float) $endPoint->latitude,
                'lng' => (float) $endPoint->longitude,
                'label' => $trackingSession->marketingProfile->code.' - '.$trackingSession->marketingProfile->user->name,
                'type' => 'end',
                'status' => $trackingSession->status->label(),
                'updated_at' => $endPoint->recorded_at?->format('d/m/Y H:i'),
                'url' => null,
                'photo_url' => ProfilePhoto::marketing($trackingSession->marketingProfile),
            ]);
        }

        // Durasi
        $durationLabel = null;
        if ($trackingSession->started_at && $trackingSession->ended_at) {
            $minutes = $trackingSession->started_at->diffInMinutes($trackingSession->ended_at);
            $hours = intdiv($minutes, 60);
            $mins = $minutes % 60;
            $durationLabel = ($hours > 0 ? $hours.'j ' : '').($mins > 0 ? $mins.'m' : '');
        }

        return view('admin.journeys.show', [
            'session' => $trackingSession,
            'points' => $points,
            'markers' => $markers,
            'path' => $path,
            'photoUrl' => ProfilePhoto::marketing($trackingSession->marketingProfile),
            'durationLabel' => $durationLabel,
        ]);
    }
}
