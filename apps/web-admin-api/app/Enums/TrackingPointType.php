<?php

declare(strict_types=1);

namespace App\Enums;

enum TrackingPointType: string
{
    case Start = 'Mulai';
    case Journey = 'Perjalanan';
    case Visit = 'Kunjungan';
    case Finish = 'Selesai';

    public function label(): string
    {
        return match ($this) {
            self::Start => 'Mulai',
            self::Journey => 'Perjalanan',
            self::Visit => 'Kunjungan',
            self::Finish => 'Selesai',
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
