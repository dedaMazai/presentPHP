<?php

namespace App\Models\Contract;

use Spatie\Enum\Enum;

/**
 * Class ContractGroup
 *
 * @method static self contract()
 * @method static self additional()
 * @method static self act()
 * @method static self protocol()
 * @method static self supply()
 * @method static self technicalReturn()
 * @method static self agencyContract()
 *
 * @package App\Models\Contract
 */
class ContractGroup extends Enum
{
    protected static function values(): array
    {
        return [
            'contract' => 'Договор',
            'additional' => 'Дополнение',
            'act' => 'Акт',
            'protocol' => 'Протокол',
            'supply' => 'Поставка',
            'technicalReturn' => 'Технический возврат (по уступке)',
            'agencyContract' => 'Агентский Договор',
        ];
    }
}
