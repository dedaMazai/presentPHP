<?php

namespace App\Console\Commands;

use App\Jobs\RetryReceiptJob;
use App\Models\Sales\FiscalReceipts;
use App\Models\Sales\PayBookingTime;
use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Sberbank\SberbankClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Class CheckFiscalStatus
 *
 * @package App\Console\Commands
 */
class CheckFiscalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:check-fiscal-status';

    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function handle(
        DynamicsCrmClient $dynamicsCrmClient,
        SberbankClient $client,
    ): void {
        $payBookingTime = PayBookingTime::where('fiscalization_complete', '=', false)->get();

        foreach ($payBookingTime as $payBooking) {
            try {
                $fiscalisation = $client->getFiscalisation($payBooking->order_id);
            } catch (\Exception $exception) {
                continue;
            }

            foreach ($fiscalisation['receipts'] as $receipt) {
                switch ($receipt['receiptStatus']) {
                    case 0:
                        $status = 'не определен';
                        break;
                    case 1:
                        $status = 'ожидается отправка или переотправка чека';
                        break;
                    case 2:
                        $status = 'чек отправлен, ожидание результата обработки';
                        break;
                    case 3:
                        $status = 'чек обработан успешно';
                        break;
                    case 4:
                        $status = 'ошибка обработки чека';
                        break;
                    case 5:
                        $status = 'ошибка отправки чека (исчерпаны попытки отправки)';
                        break;
                    case 6:
                        $status = 'некорректные данные для отправки чека';
                        break;
                    default:
                        $status = '';
                        break;
                }

                FiscalReceipts::create([
                    'order_id' => $payBooking->order_id,
                    'receipt_id' => $receipt['receiptId'],
                    'operation_id' => $receipt['operationId'],
                    'operation_type' => $receipt['operationType'],
                    'receipt_type' => $receipt['receiptType'],
                    'receipt_status_code' => $receipt['receiptStatus'],
                    'receipt_status' => $status,
                    'orig_receipt_id' => $receipt['origReceiptId'],
                    'timestamp' => Carbon::parse($receipt['timestamp']),
                    'group_code' => $receipt['groupCode'],
                    'daemon_code' => $receipt['daemonCode'],
                    'device_code' => $receipt['deviceCode'],
                    'ofd_receipt_url' => $receipt['payload']['ofdReceiptUrl'] ?? '',
                ]);

                $status = $receipt['receiptStatus'];
                $response = json_encode($fiscalisation);

                // phpcs:disable
                if ($status == 3) {
                    $payBooking->update(['fiscalization_complete' => true]);
                } elseif ($status == 0 || $status == 2) {
                    break;
                } elseif ($status == 4 || $status == 6) {
                    $message = "
                        ID заказа: $payBooking->order_id
                        E-mail клиента: $payBooking->email
                        При регистрации фискального чека возникла ошибка. Требуется ручное изменение параметров для повторной попытки фискализации $payBooking->register_do_log $response
                    ";
                    Http::post("https://" . $_SERVER["APP_URL"] . "/api/v1/feedback-appeal", [
                        'message' => $message,
                    ]);
                } elseif ($status == 1 || $status == 5) {
                    $message = "
                        ID заказа: $payBooking->order_id
                        E-mail клиента: $payBooking->email
                        При регистрации фискального чека возникла ошибка. Требуется обратиться в Сбер
                    ";

                    RetryReceiptJob::dispatch([$receipt['receiptId']], $message)
                        ->onQueue('default')
                        ->delay(now()->addHours(15));
                }
                // phpcs:enable
            }
        }
    }
}
