<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\DayName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class DayDateFilter
{
    public static function apply(Builder $query, string $dateColumn, DayName $day): Builder
    {
        $isoDay = match ($day) {
            DayName::Monday => 1,
            DayName::Tuesday => 2,
            DayName::Wednesday => 3,
            DayName::Thursday => 4,
            DayName::Friday => 5,
            DayName::Saturday => 6,
        };

        if (DB::connection()->getDriverName() === 'sqlite') {
            return $query->whereRaw("CAST(strftime('%w', {$dateColumn}) AS INTEGER) = ?", [$isoDay % 7]);
        }

        return $query->whereRaw("DAYOFWEEK({$dateColumn}) = ?", [$isoDay + 1]);
    }
}
