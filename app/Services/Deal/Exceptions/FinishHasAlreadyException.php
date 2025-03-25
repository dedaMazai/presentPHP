<?php

namespace App\Services\Deal\Exceptions;

use Exception;

/**
 * Class FinishHasAlreadyException
 *
 * @package App\Services\Deal\Exceptions
 */
class FinishHasAlreadyException extends Exception
{
    protected $message = 'Отделка уже применена к заявке';
    protected $code = 409;
}
