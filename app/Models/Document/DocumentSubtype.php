<?php

namespace App\Models\Document;

use Spatie\Enum\Enum;

/**
 * Class DocumentSubtype
 *
 * @method static self passport()
 * @method static self birthCertificate()
 * @method static self foreignDocument()
 * @method static self internationalPassport()
 * @method static self militaryId()
 * @method static self seamanPassport()
 *
 * @package App\Models\Document
 */
class DocumentSubtype extends Enum
{
    protected static function values(): array
    {
        return [
            'passport' => '1',
            'birthCertificate' => '2',
            'foreignDocument' => '4',
            'internationalPassport' => '8',
            'militaryId' => '16',
            'seamanPassport' => '32',
        ];
    }
}
