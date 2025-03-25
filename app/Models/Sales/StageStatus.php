<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class StageStatus
 *
 * @method static self closed()
 * @method static self active()
 * @method static self done()
 * @method static self wait()
 *
 * @package App\Models\Sales
 */
class StageStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'closed' => 'closed',
            'active' => 'active',
            'done' => 'done',
            'wait' => 'wait',
        ];
    }

    protected static function labels()
    {
        return [
            'closed' => 'подэтап еще не наступил',
            'active' => 'текущий активный подэтап',
            'done' => 'подэтап пройден',
            'wait' => 'ожидаются действия со стороны менеджера Пионера',
        ];
    }
}
