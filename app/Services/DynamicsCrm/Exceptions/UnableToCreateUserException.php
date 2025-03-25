<?php

namespace App\Services\DynamicsCrm\Exceptions;

use Exception;

/**
 * Class UnableToCreateUserException
 *
 * @package App\Services\DynamicsCrm\Exceptions
 */
class UnableToCreateUserException extends Exception
{
    protected $message = 'Unable to create user.';
}
