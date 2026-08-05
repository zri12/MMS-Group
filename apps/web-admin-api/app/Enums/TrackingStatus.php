<?php

declare(strict_types=1);

namespace App\Enums;

enum TrackingStatus: string
{
    case Active = 'Aktif';
    case Offline = 'Offline';
    case NotStarted = 'Belum Mulai';
    case GpsInactive = 'GPS Tidak Aktif';
    case NotScheduled = 'Tidak Dijadwalkan';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Offline => 'Offline',
            self::NotStarted => 'Belum Mulai',
            self::GpsInactive => 'GPS Tidak Aktif',
            self::NotScheduled => 'Tidak Dijadwalkan',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $options, self $case): array => $options + [$case->value => $case->label()],
            [],
        );
    }
}
