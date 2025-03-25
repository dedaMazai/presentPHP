<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class OwnerObjectType
 *
 * @method static self myself()
 * @method static self person()
 * @method static self childUnder14()
 * @method static self childOlder14()
 * @method static self childPresenter()
 *
 * @package App\Models\Sales
 */
class OwnerObjectType extends Enum
{
    protected static function values(): array
    {
        //TODO: fill values
        return [
            'myself' => '0',
            'person' => '1',
            'childUnder14' => '2',
            'childOlder14' => '3',
            'childPresenter' => '4',
        ];
    }
}
