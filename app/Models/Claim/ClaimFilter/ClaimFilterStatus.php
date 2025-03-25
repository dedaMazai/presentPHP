<?php

namespace App\Models\Claim\ClaimFilter;

use Spatie\Enum\Enum;

/**
 * Class ClaimFilterStatus
 *
 * @method static self new()
 * @method static self beingProcessed()
 * @method static self inProgress()
 * @method static self awaitingPayment()
 * @method static self closed()
 * @method static self cancelled()
 * @method static self reopened()
 * @method static self notAccepted()
 *
 * @package App\Models\Claim\ClaimFilter
 */
class ClaimFilterStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'new' => 'new',
            'beingProcessed' => 'being_processed',
            'inProgress' => 'in_progress',
            'awaitingPayment' => 'awaiting_payment',
            'closed' => 'closed',
            'cancelled' => 'cancelled',
            'reopened' => 'reopened',
            'notAccepted' => 'not_accepted',
        ];
    }
}
