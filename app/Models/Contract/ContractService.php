<?php

namespace App\Models\Contract;

use Spatie\Enum\Enum;

/**
 * Class ContractService
 *
 * @method static self sell()
 * @method static self paidBooking()
 * @method static self accountService()
 * @method static self premium()
 *
 * @package App\Models\Contract
 */
class ContractService extends Enum
{
    protected static function values(): array
    {
        //TODO: fill values
        return [
            'sell' => '020010',
            'paidBooking' => '030041',
            'accountService' => '0200160',
            'premium' => '030044',
        ];
    }

    protected static function labels(): array
    {
        //TODO: fill values
        return [
            'sell' => 'Продажа ДДУ',
            'paidBooking' => 'Платное бронирование (обычное)',
            'accountService' => 'ЖКУ  Собственнику',
            'premium' => 'Премиум',
        ];
    }
}
