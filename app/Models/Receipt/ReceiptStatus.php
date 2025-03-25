<?php

namespace App\Models\Receipt;

use Spatie\Enum\Enum;

/**
 * Class ReceiptStatus
 *
 * @method static self notPaid()
 * @method static self partlyPaid()
 * @method static self paid()
 *
 * @package App\Models\Receipt
 */
class ReceiptStatus extends Enum
{
    protected static function values(): array
    {
        //TODO: fix this when CRM API will be changed
        return [
            'notPaid' => 'Не оплачена',
            'partlyPaid' => 'Частично оплачена',
            'paid' => 'Оплачена',
        ];
    }
}
