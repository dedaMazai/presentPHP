<?php

namespace App\Models\Mortgage;

use App\Components\Enum\Traits\RegExable;
use Spatie\Enum\Enum;

/**
 * Class ProofOfIncome
 *
 * @method static self bankCertificate()
 * @method static self fullBusiness()
 * @method static self ndfl()
 * @method static self noNeeded()
 *
 * @package App\Models\Mortgage
 */
class ProofOfIncome extends Enum
{
    use RegExable;

    protected static function values(): array
    {
        return [
            'bankCertificate' => 'bank_certificate',
            'fullBusiness' => 'full_business',
            'ndfl' => 'ndfl',
            'noNeeded' => 'no_needed',
        ];
    }
}
