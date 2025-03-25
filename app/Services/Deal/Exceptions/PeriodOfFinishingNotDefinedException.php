<?php

namespace App\Services\Deal\Exceptions;

use Exception;

/**
 * Class PeriodOfFinishingNotDefined
 *
 * @package App\Services\Deal\Exceptions
 */
class PeriodOfFinishingNotDefinedException extends Exception
{
    protected $message = 'Период допустимости применения отделки не определен';
    protected $code = 409;
}
