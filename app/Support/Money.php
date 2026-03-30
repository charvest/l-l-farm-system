<?php

namespace App\Support;

final class Money
{
    public static function format(float|int|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);
        return '₱' . number_format($value, 2, '.', ',');
    }

    public static function php(float|int|string|null $amount): string
    {
        return self::format($amount);
    }
}