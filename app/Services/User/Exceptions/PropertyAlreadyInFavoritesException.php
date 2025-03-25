<?php

namespace App\Services\User\Exceptions;

use Exception;

/**
 * Class PropertyAlreadyInFavoritesException
 *
 * @package App\Services\User\Exceptions
 */
class PropertyAlreadyInFavoritesException extends Exception
{
    protected $message = 'Property already in favorites.';
}
