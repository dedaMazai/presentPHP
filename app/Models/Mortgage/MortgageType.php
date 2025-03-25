<?php

namespace App\Models\Mortgage;

use App\Components\Enum\Traits\RegExable;
use Illuminate\Support\Collection;
use Spatie\Enum\Enum;

/**
 * Class MortgageType
 *
 * @method static self developerSpecial()
 * @method static self family()
 * @method static self farEast()
 * @method static self governmentSupport()
 * @method static self military()
 * @method static self standard()
 *
 * @package App\Models\Mortgage
 */
class MortgageType extends Enum
{
    use RegExable;

    protected static function values(): array
    {
        return [
            'developerSpecial' => 'developer_special',
            'family' => 'family',
            'farEast' => 'far_east',
            'governmentSupport' => 'government_support',
            'military' => 'military',
            'standard' => 'standard',
        ];
    }

    protected static function labels(): array
    {
        return [
            'developerSpecial' => 'Спецпрограмма застройщика',
            'family' => 'Семейная ипотека',
            'farEast' => 'Дальневосточная',
            'governmentSupport' => 'Государственная поддержка 2020 (пандемия COVID-19)',
            'military' => 'Военная ипотека',
            'standard' => 'Стандартная ипотека',
        ];
    }

    public static function collectCases(): Collection
    {
        return collect(self::cases())->map(fn($k, $v) => ['label' => $k->label, 'value' => $k->value]);
    }
}
