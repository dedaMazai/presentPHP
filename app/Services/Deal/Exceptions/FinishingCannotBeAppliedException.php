<?php

namespace App\Services\Deal\Exceptions;

use Exception;

/**
 * Class FinishingCannotBeApplied
 *
 * @package App\Services\Deal\Exceptions
 */
class FinishingCannotBeAppliedException extends Exception
{
    protected $message = 'На текущий момент отделку применить нельзя';
    protected $code = 409;
}
