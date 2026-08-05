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

            $scheduleId = $this->resolveScheduleId($marketing, $data['schedule_id'] ?? null);
            $startedAt = CarbonImmutable::parse($data['started_at']);

            $activeSessionExists = TrackingSession::query()
                ->where('marketing_profile_id', $marketing->id)
                ->where('status', TrackingStatus::Active->value)
                ->exists();

            if ($activeSessionExists) {
                throw ValidationException::withMessages([
                    'tracking_session' => 'Sesi tracking aktif masih berjalan.',
                ]);
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
            default => throw ValidationException::withMessages([
                'started_at' => 'Tanggal tracking harus berada pada hari operasional.',
            ]),
        };
    }
}
