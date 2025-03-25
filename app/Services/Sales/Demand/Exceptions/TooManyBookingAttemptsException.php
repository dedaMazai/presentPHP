<?php

namespace App\Services\Sales\Demand\Exceptions;

use Exception;

/**
 * Class TooManyBookingAttemptsException
 *
 * @package App\Services\Sales\Demand\Exceptions
 */
class TooManyBookingAttemptsException extends Exception
{
    protected $message = 'Too many booking attempts.';
}
