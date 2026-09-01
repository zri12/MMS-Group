<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Enums\DayName;
use App\Enums\TrackingStatus;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\TrackingSession;
use App\Support\ApiResponse;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StartTrackingSessionAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(MarketingProfile $marketing, array $data): TrackingSession
    {
        return DB::transaction(function () use ($marketing, $data): TrackingSession {
            $existing = TrackingSession::query()
                ->with(['marketingProfile.user', 'schedule', 'latestPoint'])
                ->withCount('points')
                ->where('local_uuid', $data['local_uuid'])
                ->first();

            if ($existing) {
                if ($existing->marketing_profile_id !== $marketing->id) {
                    throw new HttpResponseException(ApiResponse::error(
                        message: 'Data tidak dapat diproses.',
                        status: 409,
                    ));
                }

                return $existing;
            }

            // Session dates and times are authoritative server time. Device
            // timestamps remain useful for GPS points but cannot open a
            // session for a previous or future operational day.
            $startedAt = CarbonImmutable::now(config('mms.timezone'));
            $scheduleId = $this->resolveScheduleId($marketing, $data['schedule_id'] ?? null);

            $activeSession = TrackingSession::query()
                ->with(['marketingProfile.user', 'schedule', 'latestPoint'])
                ->withCount('points')
                ->where('marketing_profile_id', $marketing->id)
                ->where('status', TrackingStatus::Active->value)
                ->whereDate('session_date', $startedAt->toDateString())
                ->latest('started_at')
                ->lockForUpdate()
                ->first();

            // A second request from the same device or a recovered app must
            // resume today's session. Returning it is idempotent and avoids
            // trapping the marketer behind a "session still active" error.
            if ($activeSession) {
                return $activeSession;
            }

            return TrackingSession::query()->create([
                'local_uuid' => $data['local_uuid'],
                'marketing_profile_id' => $marketing->id,
                'schedule_id' => $scheduleId,
                'session_date' => $startedAt->toDateString(),
                'day_name' => $this->dayName($startedAt),
                'started_at' => $startedAt,
                'ended_at' => null,
                'status' => TrackingStatus::Active,
                'distance_meters' => null,
                'visit_count' => 0,
            ])->load(['marketingProfile.user', 'schedule', 'latestPoint'])->loadCount('points');
        });
    }

    private function resolveScheduleId(MarketingProfile $marketing, mixed $scheduleId): ?int
    {
        if (! $scheduleId) {
            return null;
        }

        $schedule = MarketingSchedule::query()->find($scheduleId);

        if (! $schedule || $schedule->marketing_profile_id !== $marketing->id) {
            throw new HttpResponseException(ApiResponse::error(
                message: 'Data tidak ditemukan.',
                status: 404,
            ));
        }

        return $schedule->id;
    }

    private function dayName(CarbonInterface $date): DayName
    {
        return match ($date->isoWeekday()) {
            1 => DayName::Monday,
            2 => DayName::Tuesday,
            3 => DayName::Wednesday,
            4 => DayName::Thursday,
            5 => DayName::Friday,
            6 => DayName::Saturday,
            7 => config('mms.testing.allow_sunday_operations', false)
                ? DayName::Sunday
                : throw ValidationException::withMessages([
                    'started_at' => 'Tanggal tracking harus berada pada hari operasional.',
                ]),
            default => throw ValidationException::withMessages([
                'started_at' => 'Tanggal tracking harus berada pada hari operasional.',
            ]),
        };
    }
}
