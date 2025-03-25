<?php

namespace App\Services\V2\RelationshipInvite\Exception;

use Exception;

/**
 * Class InvalidPaymentTypeException
 *
 * @package App\Services\Deal\Exceptions
 */
class UserHasInvationException extends Exception
{
    protected $message = 'приглашение уже отправлено';
    protected $code = 409;
}
