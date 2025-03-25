<?php

namespace App\Models\V2\Contract;

use Spatie\Enum\Enum;

/**
 * Class ContractService
 *
 * @method static self sell()
 * @method static self paidBooking()
 * @method static self accountService()
 * @method static self sellEscrow()
 * @method static self sellCode()
 * @method static self sellCodeTwo()
 * @method static self sellCodeThree()
 * @method static self additionalCalculations()
 * @method static self adjustment()
 * @method static self additionalOption()
 * @method static self furniture()
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
            'sellEscrow' => '020011',
            'sellCode' => '020050',
            'sellCodeTwo' => '020080',
            'sellCodeThree' => '030070',
            'additionalCalculations' => '090010',
            'adjustment' => '090020',
            'additionalOption' => '030080',
            'furniture' => '030090',
        ];
    }

    protected static function labels(): array
    {
        //TODO: fill values
        return [
            'sell' => 'Продажа ДДУ',
            'paidBooking' => 'Платное бронирование (обычное)',
            'accountService' => 'ЖКУ  Собственнику',
            'sellEscrow' => 'Продажа ДДУ с Эскроу',
            'sellCode' => 'Продажа 50',
            'sellCodeTwo' => 'Продажа 80',
            'sellCodeThree' => 'Продажа 70',
            'additionalCalculations' => 'Дорасчеты БТИ (площадь)',
            'adjustment' => 'Корректировка',
            'additionalOption' => 'Дополнительная опция',
            'furniture' => 'Мебель',
        ];
    }
}
