<?php

namespace Database\Seeders;

use App\Enums\MemberApprovalStatus;
use App\Enums\SyncStatus;
use App\Models\MarketingProfile;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class MemberSeeder extends Seeder
{
    /**
     * @var list<array{name: string, suffix: string, status: MemberApprovalStatus}>
     */
    private array $memberSlots = [
        ['name' => 'Menunggu', 'suffix' => 'A', 'status' => MemberApprovalStatus::Pending],
        ['name' => 'Disetujui', 'suffix' => 'B', 'status' => MemberApprovalStatus::Approved],
        ['name' => 'Ditolak', 'suffix' => 'C', 'status' => MemberApprovalStatus::Rejected],
    ];

    public function run(): void
    {
        $this->ensureSafeEnvironment();

        $admin = User::query()->where('username', config('mms.seed.admin_username'))->firstOrFail();
        $profiles = MarketingProfile::query()->orderBy('code')->get();

        foreach ($profiles as $profileIndex => $profile) {
            foreach ($this->memberSlots as $slotIndex => $slot) {
                $memberNumber = sprintf('%04d', 468 + ($profileIndex * 3) + $slotIndex);

                Member::query()->updateOrCreate(
                    ['member_number' => $memberNumber],
                    $this->memberData($profile, $profileIndex, $slotIndex, $slot, $admin->id, $memberNumber),
                );
            }
        }
    }

    /**
     * @param  array{name: string, suffix: string, status: MemberApprovalStatus}  $slot
     * @return array<string, mixed>
     */
    private function memberData(
        MarketingProfile $profile,
        int $profileIndex,
        int $slotIndex,
        array $slot,
        int $adminId,
        string $memberNumber,
    ): array {
        $status = $slot['status'];
        $isApproved = $status === MemberApprovalStatus::Approved;
        $isRejected = $status === MemberApprovalStatus::Rejected;

        return [
            'local_uuid' => sprintf('00000000-0000-4000-8000-0000000002%02d', ($profileIndex * 3) + $slotIndex + 1),
            'marketing_profile_id' => $profile->id,
            'source_prospect_id' => null,
            'resort' => $profile->area,
            'input_date' => '2026-07-20',
            'input_time' => sprintf('%02d:%02d:00', 9 + $slotIndex, ($profileIndex % 4) * 10),
            'name' => $this->memberName($profile, $slotIndex),
            'member_number' => $memberNumber,
            'loan_number' => 'LN-'.$memberNumber,
            'address' => 'Alamat development '.$profile->code.'-'.$slot['suffix'],
            'phone' => sprintf('08121112%04d', ($profileIndex * 3) + $slotIndex + 301),
            'business' => $slotIndex === 0 ? 'Toko Kelontong' : ($slotIndex === 1 ? 'Warung Sembako' : 'Jasa Harian'),
            'loan_amount' => 4000000 + ($slotIndex * 1500000) + ($profileIndex * 100000),
            'installment_amount' => 200000 + ($slotIndex * 75000),
            'insurance_amount' => 90000 + ($slotIndex * 15000),
            'collateral' => 'BPKB motor',
            'approval_status' => $status->value,
            'member_photo_path' => null,
            'latitude' => -6.9300000 - ($profileIndex / 1000) - ($slotIndex / 10000),
            'longitude' => 107.7000000 + ($profileIndex / 1000) + ($slotIndex / 10000),
            'location_address' => $profile->area.', Kota Bandung',
            'approved_by' => $isApproved ? $adminId : null,
            'approved_at' => $isApproved ? '2026-07-20 14:00:00' : null,
            'rejected_by' => $isRejected ? $adminId : null,
            'rejected_at' => $isRejected ? '2026-07-20 14:30:00' : null,
            'rejection_reason' => $isRejected ? 'Data pengajuan belum memenuhi ketentuan development.' : null,
            'sync_status' => SyncStatus::Synced->value,
        ];
    }

    private function memberName(MarketingProfile $profile, int $slotIndex): string
    {
        if ($profile->code === 'M01') {
            return ['Herman Malik', 'Wawan Setiawan', 'Yuli Astuti'][$slotIndex];
        }

        return 'Anggota Demo '.$profile->code.' '.['Menunggu', 'Disetujui', 'Ditolak'][$slotIndex];
    }

    private function ensureSafeEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Development seeder tidak boleh dijalankan pada production.');
        }
    }
}
