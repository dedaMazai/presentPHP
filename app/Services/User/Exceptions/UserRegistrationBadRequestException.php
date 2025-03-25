<?php

namespace App\Services\User\Exceptions;

use Exception;

/**
 * Class UserRegistrationBadRequestException
 *
 * @package App\Services\User\Exceptions
 */
class UserRegistrationBadRequestException extends Exception
{
    protected $message = 'The user data is probably incorrect.';
}
