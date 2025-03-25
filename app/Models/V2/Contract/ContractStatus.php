<?php

namespace App\Models\V2\Contract;

use Spatie\Enum\Enum;

/**
 * Class ContractStatus
 *
 * @method static self active()
 * @method static self signing()
 * @method static self finished()
 * @method static self terminated()
 * @method static self didNotTakePlace()
 * @method static self registration()
 *
 * @package App\Models\Contract
 */
class ContractStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'active' => '1',
            'signing' => '2',
            'finished' => '3',
            'terminated' => '4',
            'didNotTakePlace' => '5',
            'registration' => '6',
        ];
    }

    protected static function labels(): array
    {
        return [
            'active' => 'Действующий',
            'signing' => 'Подписание',
            'finished' => 'Успешно закрыт',
            'terminated' => 'Расторгнут (Возврат)',
            'didNotTakePlace' => 'Не состоялся',
            'registration' => 'Регистрация',
        ];
    }
}
