<?php

namespace App\Models\Claim;

use Spatie\Enum\Enum;

/**
 * Class ClaimTheme
 *
 * @method static self request()
 * @method static self marketplace()
 * @method static self question()
 * @method static self appeal()
 * @method static self documents()
 * @method static self visit()
 * @method static self pass()
 * @method static self sos()
 * @method static self warranty()
 *
 * @package App\Models\Claim
 */
class ClaimTheme extends Enum
{
    protected static function values(): array
    {
        return [
            'request' => '1',
            'marketplace' => '4',
            'question' => '5',
            'appeal' => '6',
            'documents' => '7',
            'visit' => '8',
            'pass' => '10',
            'sos' => '11',
            'warranty' => '12',
        ];
    }

    protected static function labels(): array
    {
        return [
            'request' => 'Оставить заявку',
            'marketplace' => 'Маркетплейс',
            'question' => 'Задать вопрос',
            'appeal' => 'Обращение',
            'documents' => 'Запрос справок/документов',
            'visit' => 'Записаться на  прием',
            'pass' => 'Заказ пропуска',
            'sos' => 'АВАРИЙНАЯ',
            'warranty' => 'Гарантия',
        ];
    }
}
