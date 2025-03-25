<?php

namespace App\Models\Meter;

use Carbon\Carbon;

/**
 * Class MeterEnterPeriod
 *
 * @package App\Models\Meter
 */
class MeterEnterPeriod
{
    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     */
    public function __construct(
        private Carbon $startDate,
        private Carbon $endDate,
    ) {
    }

    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    public function getEndDate(): Carbon
    {
        return $this->endDate;
    }
}
