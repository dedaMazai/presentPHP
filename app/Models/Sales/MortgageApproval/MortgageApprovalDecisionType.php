<?php

namespace App\Models\Sales\MortgageApproval;

use Spatie\Enum\Enum;

/**
 * Class MortgageApprovalDecisionType
 *
 * @method static self notApproved()
 * @method static self approved()
 *
 * @package App\Models\Sales
 */
class MortgageApprovalDecisionType extends Enum
{
    protected static function values(): array
    {
        //TODO: fill values
        return [
            'notApproved' => '1',
            'approved' => '2',
        ];
    }
}
