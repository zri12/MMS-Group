<?php

declare(strict_types=1);

namespace App\Enums;

enum ScheduleStatus: string
{
    case NotVisited = 'Belum Dikunjungi';
    case InProgress = 'Berlangsung';
    case Completed = 'Selesai';
    case Canceled = 'Dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::NotVisited => 'Belum Dikunjungi',
            self::InProgress => 'Berlangsung',
            self::Completed => 'Selesai',
            self::Canceled => 'Dibatalkan',
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
