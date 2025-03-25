<?php

namespace App\Models\Sales;

use Spatie\Enum\Enum;

/**
 * Class SignStatus
 *
 * @method static self draft()
 * @method static self registration()
 * @method static self pdfCertReady()
 * @method static self registrationAwaiting()
 * @method static self pdfCertReadyItMonitoring()
 * @method static self sendingDocuments()
 * @method static self ready()
 * @method static self error()
 *
 * @package App\Models\Sales
 */
class SignStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'draft' => '41',
            'registration' => '42',
            'pdfCertReady' => '43',
            'registrationAwaiting' => '44',
            'pdfCertReadyItMonitoring' => '45',
            'sendingDocuments' => '46',
            'ready' => '47',
            'error' => '48',
        ];
    }

    protected static function labels(): array
    {
        return [
            'draft' => 'Черновик',
            'registration' => 'Выполняется регистрация',
            'pdfCertReady' => 'Получен pdf сертификата (в случае it-monitoring - анкета) - требуется его подписание',
            'registrationAwaiting' => 'Ожидается завершение регистрации',
            'pdfCertReadyItMonitoring' => 'Только it-monitoring, получен pdf сертификата - требуется его подписание',
            'sendingDocuments' => 'Выполняется отправка документов',
            'ready' => 'Готова',
            'error' => 'Ошибка',
        ];
    }
}
