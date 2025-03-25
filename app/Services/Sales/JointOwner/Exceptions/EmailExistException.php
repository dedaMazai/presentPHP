<?php

namespace App\Services\Sales\JointOwner\Exceptions;

use Exception;

/**
 * Class FinishHasAlreadyException
 *
 * @package App\Services\Deal\Exceptions
 */
class EmailExistException extends Exception
{
    protected $message = 'Клиент с таким email уже существует';
    protected $code = 422;
}
