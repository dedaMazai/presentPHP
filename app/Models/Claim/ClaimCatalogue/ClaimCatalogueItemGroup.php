<?php

namespace App\Models\Claim\ClaimCatalogue;

use Spatie\Enum\Enum;

/**
 * Class ClaimCatalogueItemGroup
 *
 * @method static self index()
 * @method static self lvl1()
 * @method static self lvl2()
 * @method static self lvl3()
 * @method static self lvl4()
 * @method static self lvl5()
 * @method static self service()
 *
 * @package App\Models\Claim\ClaimCatalogue
 */
class ClaimCatalogueItemGroup extends Enum
{
    protected static function values(): array
    {
        return [
            'index' => '0',
            'lvl1' => '5',
            'lvl2' => '10',
            'lvl3' => '15',
            'lvl4' => '20',
            'lvl5' => '25',
            'service' => '30',
        ];
    }
}
