<?php

namespace App\Services\DynamicsCrm\Exceptions;

use Exception;

/**
 * Class UnableToDeleteUserException
 *
 * @package App\Services\DynamicsCrm\Exceptions
 */
class UnableToDeleteUserException extends Exception
{
    protected $message = 'Unable to delete user.';
}
