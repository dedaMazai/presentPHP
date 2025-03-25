<?php

namespace App\Components\Enum\Traits;

/**
 * Trait RegExable
 *
 * @package App\Components\Enum\Traits
 */
trait RegExable
{
    public static function getAllowedValuesRegex(): string
    {
        return implode('|', self::toValues());
    }
}
