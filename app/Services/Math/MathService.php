<?php

namespace App\Services\Math;

class MathService
{
    public static function fibonacci($n): float
    {
        $sq5 = sqrt(5);
        $a = (1 + $sq5) / 2;
        $b = (1 - $sq5) / 2;

        return (pow($a, $n) - pow($b, $n)) / $sq5;
    }
}
