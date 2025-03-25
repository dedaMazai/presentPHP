<?php

namespace App\Models\TransactionLog;

use Spatie\Enum\Enum;

/**
 * Class TransactionLogStatus
 *
 * @method static self new()
 * @method static self registered()
 * @method static self paid()
 * @method static self failed()
 *
 * @package App\Models\TransactionLog
 */
class TransactionLogStatus extends Enum
{

}
