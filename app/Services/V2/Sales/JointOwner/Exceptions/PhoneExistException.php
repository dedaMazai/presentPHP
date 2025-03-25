<?php

namespace App\Services\V2\Sales\JointOwner\Exceptions;

use Exception;

/**
 * Class PhoneExistException
 *
 * @package App\Services\V2\Sales\JointOwner\Exceptions
 */
class PhoneExistException extends Exception
{
    protected $message = 'Клиент с указанным номером мобильного телефона существует в CRM';
    protected $code = 409;
}
