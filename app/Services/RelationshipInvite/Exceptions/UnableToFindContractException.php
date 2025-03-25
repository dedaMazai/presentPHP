<?php

namespace App\Services\RelationshipInvite\Exceptions;

use Exception;

/**
 * Class UnableToFindContractException
 *
 * @package App\Services\RelationshipInvite\Exceptions
 */
class UnableToFindContractException extends Exception
{
    protected $message = 'Unable to find contract.';
}
