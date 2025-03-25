<?php

namespace App\Models\Sales\Demand;

use Spatie\Enum\Enum;

/**
 * Class DemandBookingStatus
 *
 * @method static self notPaid()
 * @method static self paid()
 * @method static self paidOnline()
 *
 * @package App\Models\Sales\Demand
 */
class DemandBookingStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'notPaid' => '1',
            'paid' => '2',
            'paidOnline' => '4',
        ];
    }

    protected static function labels(): array
    {
        return [
            'notPaid' => 'ДБ не оплачен',
            'paid' => 'ДБ оплачен',
            'paidOnline' => 'Оплачен On-line',
        ];
    }

    public function isPaid(): bool
    {
        return $this->equals(
            DemandBookingStatus::paidOnline(),
            DemandBookingStatus::paid(),
        );
    }
}
