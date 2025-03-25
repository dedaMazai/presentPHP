<?php

namespace App\Models\TransactionLog;

use Spatie\Enum\Enum;

/**
 * Class SberbankOperationType
 *
 * @method static self approved()
 * @method static self declinedByTimeout()
 * @method static self deposited()
 * @method static self reversed()
 * @method static self refunded()
 *
 * @package App\Models\TransactionLog
 */
class SberbankOperationType extends Enum
{

}
