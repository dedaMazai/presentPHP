<?php

namespace App\Models\Document;

use Spatie\Enum\Enum;

/**
 * Class DocumentProcessingStatus
 *
 * @method static self received()
 * @method static self accepted()
 * @method static self rejected()
 *
 * @package App\Models\Document
 */
class DocumentProcessingStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'received' => '1',
            'accepted' => '2',
            'rejected' => '4'
        ];
    }

    protected static function labels(): array
    {
        return [
            'received' => 'Поступил',
            'accepted' => 'Принят',
            'rejected' => 'Отклонен'
        ];
    }
}
