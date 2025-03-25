<?php

namespace App\Models\Claim\ClaimCatalogue;

use Spatie\Enum\Enum;

/**
 * Class ClaimCatalogueItemGroup
 *
 * @method static self uk() // 1 - "УК"
 * @method static self additionalUkServices() // 2 - "Доп. услуги УК"
 * @method static self external()  // 3 - "Внешний"
 *
 * @package App\Models\Claim\ClaimCatalogue
 */
class ClaimCatalogueItemSellerType extends Enum
{
    protected static function values(): array
    {
        return [
            'uk' => '1',
            'additionalUkServices' => '2',
            'external' => '3',
        ];
    }
}
