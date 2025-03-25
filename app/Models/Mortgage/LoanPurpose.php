<?php

namespace App\Models\Mortgage;

use App\Components\Enum\Traits\RegExable;
use Spatie\Enum\Enum;

/**
 * Class LoanPurpose
 *
 * @method static self apartments()
 * @method static self commercialMortgage()
 * @method static self countryHouse()
 * @method static self primaryHousing()
 * @method static self realtyBail()
 * @method static self refinance()
 * @method static self secondaryHousing()
 * @method static self stockroom()
 *
 * @package App\Models\Mortgage
 */
class LoanPurpose extends Enum
{
    use RegExable;

    protected static function values(): array
    {
        return [
            'apartments' => 'apartments',
            'commercialMortgage' => 'commercial_mortgage',
            'countryHouse' => 'country_house',
            'primaryHousing' => 'primary_housing',
            'realtyBail' => 'realty_bail',
            'refinance' => 'refinance',
            'secondaryHousing' => 'secondary_housing',
            'stockroom' => 'stockroom',
        ];
    }
}
