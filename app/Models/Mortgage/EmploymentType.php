<?php

namespace App\Models\Mortgage;

use App\Components\Enum\Traits\RegExable;
use Spatie\Enum\Enum;

/**
 * Class EmploymentType
 *
 * @method static self businessOwner()
 * @method static self employee()
 * @method static self soleProprietor()
 *
 * @package App\Models\Mortgage
 */
class EmploymentType extends Enum
{
    use RegExable;

    protected static function values(): array
    {
        return [
            'businessOwner' => 'business_owner',
            'employee' => 'employee',
            'soleProprietor' => 'sole_proprietor',
        ];
    }
}
