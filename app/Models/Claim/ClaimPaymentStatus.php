<?php

namespace App\Models\Claim;

use Spatie\Enum\Enum;

/**
 * Class ClaimPaymentStatus
 *
 * @method static self paidOnline()
 * @method static self paid()
 * @method static self paidPartially()
 * @method static self notPaid()
 * @method static self cancelled()
 * @method static self toPay()
 *
 * @package App\Models\Claim
 */
class ClaimPaymentStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'paidOnline' => '5',
            'paid' => '1',
            'paidPartially' => '2',
            'notPaid' => '3',
            'cancelled' => '4',
            'toPay' => '6',
        ];
    }

    public function isFullyPaid(): bool
    {
        return $this->equals(
            ClaimPaymentStatus::paidOnline(),
            ClaimPaymentStatus::paid(),
        );
    }

    public function isCancelable(): bool
    {
        return $this->equals(
            ClaimPaymentStatus::toPay(),
            ClaimPaymentStatus::notPaid(),
        );
    }
}
