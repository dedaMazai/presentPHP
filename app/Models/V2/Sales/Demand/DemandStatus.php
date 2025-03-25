<?php

namespace App\Models\V2\Sales\Demand;

use Spatie\Enum\Enum;

/**
 * Class DemandStatus
 *
 * @method static self open()
 * @method static self queue()
 * @method static self contract()
 * @method static self timeIsUp()
 * @method static self unableToContact()
 * @method static self notInterested()
 * @method static self canceled()
 * @method static self reservation()
 * @method static self confirmation()
 * @method static self agreement()
 * @method static self pending()
 * @method static self expired()
 * @method static self selt()
 *
 * @package App\Models\Sales\Demand
 */
class DemandStatus extends Enum
{
    protected static function values(): array
    {
        //TODO: fill values
        /*
614 950 000 - Клиент компании
100 000 008 - Отложенный спрос
100 000 000 - Дорого
100 000 001 - Не устраивает срок сдачи
100 000 002 - Не устраивает расположение объекта
100 000 003 - Мониторинг или Риелтор
100 000 004 - Не работаем с субсидией
100 000 005 - Не работаем с военной ипотекой
100 000 006 - Нет предложения
100 000 007 - Купил у конкурента
614 950 001 - Банк не одобрил ипотеку
614 950 002 - Купил вторичку
614 950 003 - Не устраивает формат Договора
614 950 004 - Не оплачено
         */
        return [
            'open' => '1',
            'queue' => '2',
            'contract' => '3',
            'timeIsUp' => '4',
            'unableToContact' => '5',
            'notInterested' => '6',
            'canceled' => '7',
            'reservation' => '8',
            'confirmation' => '16',
            'agreement' => '32',
            'pending' => '64',
            'expired' => '512',
            'selt' => '1024',
        ];
    }

    protected static function labels(): array
    {
        //TODO: fill values
        return [
            'open' => 'Открыта',
            'queue' => 'В очереди',
            'contract' => 'Договор',
            'timeIsUp' => 'Истекло время',
            'unableToContact' => 'Невозможно связаться',
            'notInterested' => 'Больше не интересно (Передумал покупать)',
            'canceled' => 'Отменено (Не выяснено)',
            'reservation' => 'Бронь',
            'confirmation' => 'Подтверждение',
            'agreement' => 'Согласование',
            'pending' => 'В ожидании',
            'expired' => 'Просрочена',
            'selt' => 'СЭЛТ',
        ];
    }
}
