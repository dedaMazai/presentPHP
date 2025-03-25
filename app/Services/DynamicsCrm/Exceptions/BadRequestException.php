<?php

namespace App\Services\DynamicsCrm\Exceptions;

use Exception;

/**
 * Class BadRequestException
 *
 * @package App\Services\DynamicsCrm\Exceptions
 */
class BadRequestException extends Exception
{
    protected $message = 'Bad Request.';
}
