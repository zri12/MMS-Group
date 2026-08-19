<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Enums\SyncStatus;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\OperationalReportAttachment;
use App\Support\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class CreateOperationalReportAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(MarketingProfile $marketing, array $data): DailyOperationalReport
    {
        return DB::transaction(function () use ($marketing, $data): DailyOperationalReport {
            $existing = DailyOperationalReport::query()
                ->with('marketingProfile.user')
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

            $report = DailyOperationalReport::query()->create([
                'local_uuid' => $data['local_uuid'],
                'marketing_profile_id' => $marketing->id,
                'report_date' => $data['date'],
                'report_time' => $data['time'],
                'day_name' => $data['day'],
                'resort' => $data['resort'],
                'storting' => $data['storting'],
                'insurance_amount' => $data['insurance_amount'],
                'drop_amount' => $data['drop'],
                'withdrawal_saving' => $data['withdrawal_saving'],
                'previous_target_amount' => $data['previous_target_amount'],
                'previous_target_people' => $data['previous_target_people'],
                'incoming_target_amount' => $data['incoming_target_amount'],
                'incoming_target_people' => $data['incoming_target_people'],
                'outgoing_target_amount' => $data['outgoing_target_amount'],
                'outgoing_target_people' => $data['outgoing_target_people'],
                'total_target_amount' => $data['total_target_amount'],
                'total_target_people' => $data['total_target_people'],
                'new_drop' => $data['new_drop'],
                'continued_drop' => $data['continued_drop'],
                'notes' => $data['notes'] ?? null,
                'sync_status' => SyncStatus::Synced,
            ]);

            foreach ($data['attachments'] ?? [] as $attachment) {
                OperationalReportAttachment::query()->create([
                    'daily_operational_report_id' => $report->id,
                    'type' => $attachment['type'],
                    'photo_path' => $attachment['photo']->store('operational-report-attachments', 'public'),
                    'caption' => $attachment['caption'] ?? null,
                    'uploaded_at' => now(),
                ]);
            }

            return $report->load(['marketingProfile.user', 'attachments']);
        });
    }
}
