<?php

declare(strict_types=1);

namespace App\Enums;

enum DayName: string
{
    case Monday = 'Senin';
    case Tuesday = 'Selasa';
    case Wednesday = 'Rabu';
    case Thursday = 'Kamis';
    case Friday = 'Jumat';
    case Saturday = 'Sabtu';
    case Sunday = 'Minggu';

    public function label(): string
    {
        return match ($this) {
            self::Monday => 'Senin',
            self::Tuesday => 'Selasa',
            self::Wednesday => 'Rabu',
            self::Thursday => 'Kamis',
            self::Friday => 'Jumat',
            self::Saturday => 'Sabtu',
            self::Sunday => 'Minggu',
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
     * @return list<string>
     */
    public static function operationalValues(): array
    {
        return array_column(self::operationalCases(), 'value');
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

    /**
     * @return array<string, string>
     */
    public static function operationalOptions(): array
    {
        return array_reduce(
            self::operationalCases(),
            static fn (array $options, self $case): array => $options + [$case->value => $case->label()],
            [],
        );
    }

    /**
     * @return list<self>
     */
    private static function operationalCases(): array
    {
        return array_values(array_filter(
            self::cases(),
            static fn (self $day): bool => $day !== self::Sunday || config('mms.testing.allow_sunday_operations', false),
        ));
    }
}
