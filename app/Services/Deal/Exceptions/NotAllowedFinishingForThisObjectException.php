<?php

namespace App\Services\Deal\Exceptions;

use Exception;

/**
 * Class NotAllowedFinishingForThisObjectException
 *
 * @package App\Services\Deal\Exceptions
 */
class NotAllowedFinishingForThisObjectException extends Exception
{
    protected $message = 'Для данного объекта не допускается применить отделку';
    protected $code = 409;
}
