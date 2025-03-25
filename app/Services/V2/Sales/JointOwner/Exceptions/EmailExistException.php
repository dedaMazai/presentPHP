<?php

namespace App\Services\V2\Sales\JointOwner\Exceptions;

use Exception;

/**
 * Class FinishHasAlreadyException
 *
 * @package App\Services\V2\Sales\JointOwner\Exceptions
 */
class EmailExistException extends Exception
{
    protected $message = 'Клиент с таким email уже существует';
    protected $code = 422;
}
