<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\DayName;
use App\Enums\MemberApprovalStatus;
use App\Enums\ProspectStatus;
use App\Enums\ScheduleStatus;
use App\Enums\SyncStatus;
use App\Enums\TrackingPointType;
use App\Enums\TrackingStatus;
use App\Enums\UserRole;
use App\Enums\VisitResult;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DomainEnumTest extends TestCase
{
    /**
     * @param  class-string  $enumClass
     * @param  list<string>  $expectedValues
     * @param  array<string, string>  $expectedLabels
     */
    #[DataProvider('enumProvider')]
    public function test_domain_enum_values_and_labels_are_stable(
        string $enumClass,
        array $expectedValues,
        array $expectedLabels,
    ): void {
        $this->assertSame($expectedValues, $enumClass::values());
        $this->assertSame($expectedValues, array_values(array_unique($enumClass::values())));

        foreach ($enumClass::cases() as $case) {
            $this->assertSame($expectedLabels[$case->value], $case->label());
        }

        $this->assertSame($expectedLabels, $enumClass::options());
    }

    /**
     * @return array<string, array{0: class-string, 1: list<string>, 2: array<string, string>}>
     */
    public static function enumProvider(): array
    {
        return [
            'user_role' => [
                UserRole::class,
                ['admin', 'marketing'],
                [
                    'admin' => 'Administrator',
                    'marketing' => 'Marketing',
                ],
            ],
            'day_name' => [
                DayName::class,
                ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                [
                    'Senin' => 'Senin',
                    'Selasa' => 'Selasa',
                    'Rabu' => 'Rabu',
                    'Kamis' => 'Kamis',
                    'Jumat' => 'Jumat',
                    'Sabtu' => 'Sabtu',
                ],
            ],
            'member_approval_status' => [
                MemberApprovalStatus::class,
                ['Menunggu', 'Disetujui', 'Ditolak'],
                [
                    'Menunggu' => 'Menunggu',
                    'Disetujui' => 'Disetujui',
                    'Ditolak' => 'Ditolak',
                ],
            ],
            'prospect_status' => [
                ProspectStatus::class,
                ['Baru', 'Tertarik', 'Perlu Follow Up', 'Tidak Tertarik', 'Selesai'],
                [
                    'Baru' => 'Baru',
                    'Tertarik' => 'Tertarik',
                    'Perlu Follow Up' => 'Perlu Follow Up',
                    'Tidak Tertarik' => 'Tidak Tertarik',
                    'Selesai' => 'Selesai',
                ],
            ],
            'visit_result' => [
                VisitResult::class,
                ['Berhasil Bertemu', 'Tidak Bertemu', 'Transaksi Selesai'],
                [
                    'Berhasil Bertemu' => 'Berhasil Bertemu',
                    'Tidak Bertemu' => 'Tidak Bertemu',
                    'Transaksi Selesai' => 'Transaksi Selesai',
                ],
            ],
            'schedule_status' => [
                ScheduleStatus::class,
                ['Belum Dikunjungi', 'Berlangsung', 'Selesai', 'Dibatalkan'],
                [
                    'Belum Dikunjungi' => 'Belum Dikunjungi',
                    'Berlangsung' => 'Berlangsung',
                    'Selesai' => 'Selesai',
                    'Dibatalkan' => 'Dibatalkan',
                ],
            ],
            'tracking_status' => [
                TrackingStatus::class,
                ['Aktif', 'Offline', 'Belum Mulai', 'GPS Tidak Aktif', 'Tidak Dijadwalkan'],
                [
                    'Aktif' => 'Aktif',
                    'Offline' => 'Offline',
                    'Belum Mulai' => 'Belum Mulai',
                    'GPS Tidak Aktif' => 'GPS Tidak Aktif',
                    'Tidak Dijadwalkan' => 'Tidak Dijadwalkan',
                ],
            ],
            'tracking_point_type' => [
                TrackingPointType::class,
                ['Mulai', 'Perjalanan', 'Kunjungan', 'Selesai'],
                [
                    'Mulai' => 'Mulai',
                    'Perjalanan' => 'Perjalanan',
                    'Kunjungan' => 'Kunjungan',
                    'Selesai' => 'Selesai',
                ],
            ],
            'sync_status' => [
                SyncStatus::class,
                ['Tersinkronisasi', 'Menunggu Sinkronisasi', 'Gagal'],
                [
                    'Tersinkronisasi' => 'Tersinkronisasi',
                    'Menunggu Sinkronisasi' => 'Menunggu Sinkronisasi',
                    'Gagal' => 'Gagal',
                ],
            ],
        ];
    }
}
