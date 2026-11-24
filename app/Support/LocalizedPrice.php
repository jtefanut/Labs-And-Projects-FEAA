<?php

namespace App\Support;

use Illuminate\Support\Number;

class LocalizedPrice
{
    public static function format(float|int|string|null $ron): string
    {
        $value = (float) ($ron ?? 0);
        $english = app()->getLocale() === 'en';

        if ($english) {
            $value /= (float) config('pricing.ron_per_usd', 4.6);
        }

        return Number::currency($value, $english ? 'USD' : 'RON', $english ? 'en_US' : 'ro_RO');
    }
}
