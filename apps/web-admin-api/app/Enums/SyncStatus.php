<?php

declare(strict_types=1);

namespace App\Enums;

enum SyncStatus: string
{
    case Synced = 'Tersinkronisasi';
    case Pending = 'Menunggu Sinkronisasi';
    case Failed = 'Gagal';

    public function label(): string
    {
        return match ($this) {
            self::Synced => 'Tersinkronisasi',
            self::Pending => 'Menunggu Sinkronisasi',
            self::Failed => 'Gagal',
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
