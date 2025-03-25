<?php

namespace App\Models\Claim\ClaimCatalogue;

use Spatie\Enum\Enum;

/**
 * Class ClaimCatalogueItemSelectOption
 *
 * @method static self one()
 * @method static self multiple()
 * @method static self service()
 *
 * @package App\Models\Claim\ClaimCatalogue
 */
class ClaimCatalogueItemSelectOption extends Enum
{
    protected static function values(): array
    {
        return [
            'one' => '1',
            'multiple' => '2',
            'service' => '3',
        ];
    }
}
