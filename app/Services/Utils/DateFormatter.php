<?php

namespace App\Services\Utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;

class DateFormatter
{
    public static function birthDateFormatter(string $date): string
    {
        $birthDate = isset($date) ?
            Carbon::parse($date) :
            Carbon::createFromTimestamp(0);

        return $birthDate->age;
    }
}
