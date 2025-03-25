<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class DiscountType
 *
 * @method static self select()
 * @method static self required()
 * @method static self info()
 * @method static self amountFromQuantity()
 * @method static self amountFromTotal()
 * @method static self percentFromTotal()
 * @method static self amountFromFirstPayment()
 * @method static self amountFromAfterFirstPayment()
 * @method static self multi()
 *
 * @package App\Models\Sales
 */
class DiscountType extends Enum
{
    protected static function values(): array
    {
        return [
            'amountFromQuantity' => '5',
            'amountFromTotal' => '9',
            'percentFromTotal' => '10',
            'amountFromFirstPayment' => '11',
            'amountFromAfterFirstPayment' => '12',
            'multi' => '16',
        ];
    }

    protected static function labels(): array
    {
        return [
            'amountFromQuantity' => 'Сумма с квадратного метра',
            'amountFromTotal' => 'Сумма с общей стоимости',
            'percentFromTotal' => 'Процент со стоимости',
            'amountFromFirstPayment' => 'С первоначального взноса',
            'amountFromAfterFirstPayment' => 'С остатка после первоначального взноса',
            'multi' => 'Мультискидка',
        ];
    }
}
