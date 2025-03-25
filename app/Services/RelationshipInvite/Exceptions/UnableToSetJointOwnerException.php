<?php

namespace App\Services\RelationshipInvite\Exceptions;

use Exception;

/**
 * Class UnableToSetJointOwnerException
 *
 * @package App\Services\RelationshipInvite\Exceptions
 */
class UnableToSetJointOwnerException extends Exception
{
    protected $message = 'Unable to set joint owner.';
}
