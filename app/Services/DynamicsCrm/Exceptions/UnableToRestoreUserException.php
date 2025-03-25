<?php

namespace App\Services\DynamicsCrm\Exceptions;

use Exception;

/**
 * Class UnableToRestoreUserException
 *
 * @package App\Services\DynamicsCrm\Exceptions
 */
class UnableToRestoreUserException extends Exception
{
    protected $message = 'Unable to restore user.';
}
