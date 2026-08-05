<?php

declare(strict_types=1);

namespace App\Enums;

enum ProspectStatus: string
{
    case New = 'Baru';
    case Interested = 'Tertarik';
    case FollowUp = 'Perlu Follow Up';
    case NotInterested = 'Tidak Tertarik';
    case Completed = 'Selesai';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Baru',
            self::Interested => 'Tertarik',
            self::FollowUp => 'Perlu Follow Up',
            self::NotInterested => 'Tidak Tertarik',
            self::Completed => 'Selesai',
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
