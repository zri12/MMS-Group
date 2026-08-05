<?php

declare(strict_types=1);

namespace App\Actions\Api\V1;

use App\Enums\ProspectStatus;
use App\Enums\SyncStatus;
use App\Models\MarketingProfile;
use App\Models\Prospect;
use App\Models\VisitReport;
use App\Support\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateVisitReportAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(MarketingProfile $marketing, array $data, ?UploadedFile $photo = null): VisitReport
    {
        return DB::transaction(function () use ($marketing, $data, $photo): VisitReport {
            $existing = VisitReport::query()
                ->with(['prospect', 'marketingProfile.user'])
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

            $prospect = Prospect::query()
                ->whereKey($data['prospect_id'])
                ->lockForUpdate()
                ->first();

            if (! $prospect || $prospect->marketing_profile_id !== $marketing->id) {
                throw new HttpResponseException(ApiResponse::error(
                    message: 'Data tidak ditemukan.',
                    status: 404,
                ));
            }

            $photoPath = $photo?->store('visit-reports', 'public');

            $report = VisitReport::query()->create([
                'local_uuid' => $data['local_uuid'],
                'prospect_id' => $prospect->id,
                'marketing_profile_id' => $marketing->id,
                'visit_date' => $data['date'],
                'visit_time' => $data['time'],
                'day_name' => $data['day'],
                'visit_purpose' => $data['visit_purpose'],
                'visit_result' => $data['visit_result'],
                'prospect_status' => $data['prospect_status'],
                'notes' => $data['notes'] ?? null,
                'follow_up_date' => $data['follow_up_date'] ?? null,
                'photo_path' => $photoPath,
                'photo_caption' => $data['photo_caption'] ?? null,
                'resort' => $data['resort'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'location_address' => $data['location_address'] ?? null,
                'sync_status' => SyncStatus::Synced,
            ]);

            $prospect->forceFill([
                'status' => ProspectStatus::from($data['prospect_status']),
            ])->save();

            return $report->load(['prospect', 'marketingProfile.user']);
        });
    }
}
