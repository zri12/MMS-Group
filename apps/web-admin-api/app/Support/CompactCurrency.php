<?php

declare(strict_types=1);

namespace App\Support;

class CompactCurrency
{
    public static function format(int $amount): string
    {
        if ($amount >= 1_000_000_000) {
            return 'Rp '.self::number($amount / 1_000_000_000).' M';
        }

        if ($amount >= 1_000_000) {
            return 'Rp '.self::number($amount / 1_000_000).' jt';
        }

        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    private static function number(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1, ',', '.'), '0'), ',');
    }
}
