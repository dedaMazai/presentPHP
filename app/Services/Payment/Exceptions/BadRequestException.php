<?php

namespace App\Services\Payment\Exceptions;

use Exception;

/**
 * Class BadRequestException
 *
 * @package App\Services\Payment\Exceptions
 */
class BadRequestException extends Exception
{
    protected $message = 'Bad Request.';
}
