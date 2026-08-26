<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Enums\OperationalAttachmentType;
use App\Enums\ProspectStatus;
use App\Enums\ScheduleStatus;
use App\Enums\SyncStatus;
use App\Enums\TrackingPointType;
use App\Enums\TrackingStatus;
use App\Models\DailyOperationalReport;
use App\Models\MarketingProfile;
use App\Models\MarketingSchedule;
use App\Models\Member;
use App\Models\OperationalRecap;
use App\Models\OperationalRecapRow;
use App\Models\OperationalReportAttachment;
use App\Models\Prospect;
use App\Models\TrackingPoint;
use App\Models\TrackingSession;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'development', 'testing'])) {
            throw new RuntimeException('Demo data hanya boleh dijalankan pada local/development/testing.');
        }

        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $today = CarbonImmutable::today(config('app.timezone'));
        $day = DayName::tryFrom($today->locale('id')->isoFormat('dddd')) ?? DayName::Saturday;
        $names = ['Andi Pratama', 'Budi Santoso', 'Citra Ramadhan', 'Deni Kurniawan', 'Eka Putra', 'Fajar Nugraha'];
        $areas = ['Cimahi Tengah', 'Cimahi Selatan', 'Cimahi Utara', 'Bandung Barat', 'Padalarang', 'Batujajar'];
        $drop = [22_000_000, 20_000_000, 21_500_000, 19_000_000, 20_500_000, 22_000_000];
        $storting = [17_500_000, 16_000_000, 18_000_000, 15_500_000, 16_000_000, 15_500_000];
        $incoming = [8_000_000, 7_500_000, 8_500_000, 6_500_000, 7_000_000, 7_500_000];
        $outgoing = [5_000_000, 5_500_000, 4_000_000, 5_000_000, 5_000_000, 5_500_000];
        $profiles = collect();

        foreach ($names as $index => $name) {
            $code = 'PDL'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
            $profile = MarketingProfile::query()->firstOrCreate(['code' => $code], [
                'display_name' => $name,
                'area' => $areas[$index],
                'phone' => '0812000000'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            ]);
            $profile->update(['display_name' => $name, 'area' => $areas[$index]]);
            $profiles->push($profile);

            foreach (range(0, 6) as $offset) {
                $date = $today->subDays($offset);
                $report = DailyOperationalReport::query()->updateOrCreate(['local_uuid' => sprintf('20000000-0000-4000-8000-%012d', ($index + 1) * 10 + $offset)], [
                    'marketing_profile_id' => $profile->id, 'report_date' => $date, 'report_time' => '16:00:00', 'day_name' => $day->value,
                    'resort' => $profile->area, 'storting' => $storting[$index], 'insurance_amount' => 150_000, 'drop_amount' => $drop[$index],
                    'withdrawal_saving' => 100_000, 'previous_target_amount' => 5_000_000, 'previous_target_people' => 2,
                    'incoming_target_amount' => $incoming[$index], 'incoming_target_people' => 3, 'outgoing_target_amount' => $outgoing[$index],
                    'outgoing_target_people' => 1, 'total_target_amount' => $incoming[$index] + $outgoing[$index], 'total_target_people' => 4,
                    'new_drop' => 2_000_000, 'continued_drop' => $drop[$index] - 2_000_000, 'notes' => 'Data demo PDL.', 'sync_status' => SyncStatus::Synced,
                ]);

                if ($offset === 0) {
                    foreach ([OperationalAttachmentType::Disbursement, OperationalAttachmentType::TransferProof] as $type) {
                        OperationalReportAttachment::query()->updateOrCreate(['daily_operational_report_id' => $report->id, 'type' => $type->value], [
                            'photo_path' => $type === OperationalAttachmentType::Disbursement ? 'demo/pencairan/pencairan-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).'.jpg' : 'demo/transfer/transfer-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).'.jpg',
                            'caption' => 'Dokumen demo '.$code, 'uploaded_at' => $today->setTime(16, 0),
                        ]);
                    }
                }
            }

            foreach (range(1, 3) as $memberIndex) {
                Member::query()->updateOrCreate(['local_uuid' => sprintf('30000000-0000-4000-8000-%012d', ($index + 1) * 10 + $memberIndex)], [
                    'marketing_profile_id' => $profile->id, 'resort' => $profile->area, 'input_date' => $today, 'input_time' => '10:00:00',
                    'name' => 'Anggota Demo '.($index + 1).'-'.$memberIndex, 'member_number' => 'D'.($index + 1).$memberIndex.'001', 'loan_number' => 'L'.($index + 1).$memberIndex.'001',
                    'address' => $profile->area, 'phone' => '081200000'.($index + 1).$memberIndex, 'business' => 'Usaha Demo', 'loan_amount' => 10_000_000,
                    'installment_amount' => 500_000, 'insurance_amount' => 100_000, 'collateral' => 'BPKB', 'approval_status' => MemberApprovalStatus::Pending,
                    'sync_status' => SyncStatus::Synced,
                ]);
            }

            $prospect = Prospect::query()->updateOrCreate(['local_uuid' => sprintf('40000000-0000-4000-8000-%012d', $index + 1)], [
                'marketing_profile_id' => $profile->id, 'name' => 'Prospek Demo '.($index + 1), 'phone' => '081200001'.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'address' => $profile->area, 'business' => 'Usaha Demo', 'status' => ProspectStatus::Interested, 'initial_visit_result' => 'Data demo',
                'resort' => $profile->area, 'input_date' => $today, 'input_time' => '09:00:00', 'sync_status' => SyncStatus::Synced,
            ]);
            MarketingSchedule::query()->updateOrCreate(['marketing_profile_id' => $profile->id, 'schedule_date' => $today, 'start_time' => '09:00:00'], [
                'prospect_id' => $prospect->id, 'day_name' => $day->value, 'end_time' => '10:00:00', 'consumer_name_snapshot' => $prospect->name,
                'agenda' => 'Kunjungan demo', 'area' => $profile->area, 'status' => ScheduleStatus::InProgress, 'created_by' => $admin->id,
            ]);
            $session = TrackingSession::query()->updateOrCreate(['local_uuid' => sprintf('50000000-0000-4000-8000-%012d', $index + 1)], [
                'marketing_profile_id' => $profile->id, 'session_date' => $today, 'day_name' => $day->value, 'started_at' => $today->setTime(8, 0),
                'ended_at' => $index < 3 ? null : $today->setTime(15, 0), 'status' => $index < 3 ? TrackingStatus::Active : TrackingStatus::Offline,
                'distance_meters' => 5400 + $index * 500, 'visit_count' => 2,
            ]);
            foreach (range(0, 4) as $pointIndex) {
                TrackingPoint::query()->updateOrCreate(['local_uuid' => sprintf('60000000-0000-4000-%04d-%012d', $index + 1, $pointIndex + 1)], [
                    'tracking_session_id' => $session->id, 'latitude' => -6.8722 - ($index * .002) - ($pointIndex * .0003), 'longitude' => 107.5422 + ($index * .002) + ($pointIndex * .0003),
                    'accuracy_meters' => 8, 'point_type' => $pointIndex === 0 ? TrackingPointType::Start : ($pointIndex === 4 ? TrackingPointType::Finish : TrackingPointType::Journey),
                    'recorded_at' => $today->setTime(8 + $pointIndex, 0), 'received_at' => $today->setTime(8 + $pointIndex, 0),
                ]);
            }
        }

        $recap = OperationalRecap::query()->updateOrCreate(['recap_date' => $today], ['report_number' => 'DEMO-'.$today->format('Ymd'), 'day_name' => $day->value, 'status' => 'Selesai', 'created_by' => $admin->id]);
        $profiles->each(function (MarketingProfile $profile, int $index) use ($recap, $drop, $storting): void {
            OperationalRecapRow::query()->updateOrCreate(['operational_recap_id' => $recap->id, 'marketing_profile_id' => $profile->id], [
                'mg' => $profile->code, 'members_l' => 3, 'members_m' => 0, 'members_k' => 0, 'members_s' => 0, 'target_previous' => 0, 'target_incoming' => 0,
                'target_outgoing' => 0, 'target_s' => 0, 'drop_previous' => 0, 'drop_current' => $drop[$index], 'drop_total' => $drop[$index],
                'storting_previous' => 0, 'storting_current' => $storting[$index], 'storting_total' => $storting[$index], 'previous_circulation' => 0,
                'current_circulation' => [39_500_000, 36_000_000, 39_500_000, 34_500_000, 36_500_000, 37_500_000][$index], 'followed_by' => 'Demo', 'morning_cash' => 0,
            ]);
        });

        $this->seedCurrentTrackingForMarketing($today, $day);
    }

    private function seedCurrentTrackingForMarketing(CarbonImmutable $today, DayName $day): void
    {
        MarketingProfile::query()
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->orderBy('code')
            ->get()
            ->values()
            ->each(function (MarketingProfile $profile, int $index) use ($today, $day): void {
                $session = TrackingSession::query()->updateOrCreate(
                    [
                        'marketing_profile_id' => $profile->id,
                        'session_date' => $today->toDateString(),
                    ],
                    [
                        'local_uuid' => sprintf('70000000-0000-4000-8000-%012d', ((int) $today->format('Ymd') * 100) + $index + 1),
                        'day_name' => $day->value,
                        'started_at' => $today->setTime(8, 0)->addMinutes($index * 5),
                        'ended_at' => $index < 5 ? null : $today->setTime(15, 0)->addMinutes($index * 5),
                        'status' => $index < 5 ? TrackingStatus::Active->value : TrackingStatus::Offline->value,
                        'distance_meters' => 4200 + ($index * 350),
                        'visit_count' => 1 + ($index % 3),
                    ],
                );

                $latitude = -6.914744 + ($index * 0.006);
                $longitude = 107.609810 + ($index * 0.007);
                $recordedAt = $today->setTime(8, 20)->addMinutes($index * 5);

                TrackingPoint::query()->updateOrCreate(
                    [
                        'local_uuid' => sprintf('71000000-0000-4000-8000-%012d', ((int) $today->format('Ymd') * 100) + $index + 1),
                    ],
                    [
                        'tracking_session_id' => $session->id,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'accuracy_meters' => 8.5,
                        'speed_mps' => 1.2,
                        'heading' => 90,
                        'altitude_meters' => 730,
                        'address' => $profile->area.', Kota Bandung',
                        'point_type' => TrackingPointType::Journey->value,
                        'recorded_at' => $recordedAt,
                        'received_at' => $recordedAt->addSeconds(5),
                    ],
                );
            });
    }
}
