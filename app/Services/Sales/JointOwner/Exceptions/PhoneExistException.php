<?php

namespace App\Services\Sales\JointOwner\Exceptions;

use Exception;

/**
 * Class PhoneExistException
 *
 * @package App\Services\Sales\JointOwner\Exceptions
 */
class PhoneExistException extends Exception
{
    protected $message = 'Клиент с указанным номером мобильного телефона существует в CRM';
    protected $code = 409;
}
