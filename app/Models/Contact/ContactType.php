<?php

namespace App\Models\Contact;

use Spatie\Enum\Enum;

/**
 * Class ContactType
 *
 * @method static self map()
 * @method static self email()
 * @method static self phone()
 *
 * @package App\Models\Contact
 */
class ContactType extends Enum
{
    protected static function values(): array
    {
        return [
            'map' => 'map',
            'email' => 'email',
            'phone' => 'phone',
        ];
    }

    protected static function labels()
    {
        return [
            'map' => 'Карта',
            'email' => 'Email',
            'phone' => 'Телефон',
        ];
    }
}
