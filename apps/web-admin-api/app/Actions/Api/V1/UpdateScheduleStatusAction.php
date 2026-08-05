<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Enums\ScheduleStatus;
use App\Models\MarketingSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateScheduleStatusAction
{
    /**
     * @var array<string, list<string>>
     */
    private array $allowedTransitions = [
        'Belum Dikunjungi' => ['Berlangsung', 'Dibatalkan'],
        'Berlangsung' => ['Selesai', 'Dibatalkan'],
        'Selesai' => [],
        'Dibatalkan' => [],
    ];

    public function execute(MarketingSchedule $schedule, ScheduleStatus $targetStatus): MarketingSchedule
    {
        return DB::transaction(function () use ($schedule, $targetStatus): MarketingSchedule {
            $locked = MarketingSchedule::query()
                ->whereKey($schedule->id)
                ->lockForUpdate()
                ->firstOrFail();

            $current = $locked->status->value;

            if (! in_array($targetStatus->value, $this->allowedTransitions[$current] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Transisi status tidak tersedia.',
                ]);
            }

            $locked->forceFill([
                'status' => $targetStatus,
            ])->save();

            return $locked->refresh()->load(['marketingProfile.user', 'prospect']);
        });
    }
}
