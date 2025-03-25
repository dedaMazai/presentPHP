<?php

namespace App\Http\Api\External\V1\Controllers\Payment;

use App\Http\Api\External\V1\Controllers\Controller;
use App\Models\TransactionLog\TransactionLog;
use App\Services\Payment\Exceptions\BadRequestException;
use App\Services\Payment\PaymentService;
use App\Services\Payment\SBPPaymentService;
use App\Services\TransactionLog\TransactionLogService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class SBPPaymentController extends Controller
{
    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function payByCard(
        string $accountNumber,
        Request $request,
        SBPPaymentService $service,
    ): Response {
        $orderId = str_shuffle('0123456789');

        if ($request->input('claim_id')) {
            $claim = $service->findClaim($request->input('claim_id'));
        }

        logger()->debug('PSBPaymentController::payByCard', [
            'amount' => $request->input('amount'),
        ]);

        if (isset($claim)) {
            $merchant = $service->getClaimCredetionals($claim);

            $transactionLog = $service->createTransactionLogWithClaim(
                $accountNumber,
                $request,
                $this->getAuthUser(),
                $claim,
                $orderId
            );

            $paymentItem = $service->createClaimPaymentItem(
                $this->getAuthUser(),
                $claim,
                $accountNumber,
                $orderId,
                $merchant
            );

            $result = $service->createPayment($transactionLog, $paymentItem);
        } else {
            $this->validate($request, [
                'amount' => 'required_without:amount|integer|min:1',
            ]);

            $merchant = $service->getCredetionals($accountNumber);

            $transactionLog = $service->createTransactionLog(
                $accountNumber,
                $request,
                $this->getAuthUser(),
                $orderId,
                $merchant['account_seller_id']
            );

            $paymentItem = $service->createPaymentItem(
                $this->getAuthUser(),
                $request,
                $accountNumber,
                $orderId,
                $merchant['credetionals']
            );

            $result = $service->createPayment($transactionLog, $paymentItem);
        }

        TransactionLog::where('psb_order_id', '=', $orderId)->update(['qr_id' => $result['qr_id']]);


        return $this->response(['url' => $result['url']]);
    }

    public function callback(
        Request $request,
        PaymentService $paymentService,
    ): Response {
        $transactionLog = TransactionLog::where('qr_id', '=', $request->input('QR_ID'));
        $account_number = $transactionLog->value('account_number');
        $amount = $transactionLog->value('amount');
        $psb_order_id = $transactionLog->value('psb_order_id');
        $created_at = $transactionLog->value('created_at');
        $paymentMethod = 'SBP';

        $path = storage_path('logs/sbp_payments.log');
        $text = date('Y-m-d H:i:s').' | request: '. $_SERVER['REQUEST_URI']. " | " .
            $request->input('ORDER') . '|' . $transactionLog->value('account_number') . '|' .
            $transactionLog->value('user_id') . '|' . $transactionLog->value('amount');

        file_put_contents($path, PHP_EOL . $text, FILE_APPEND);

        try {
            if ($transactionLog->value('claim_id')) {
                if ($request->input('RESULT') == 0) {
                    $this->getLogger()->info($text);
                    $psb_order_id = $transactionLog->value('psb_order_id');
                    $paymentService->makeTransactionPSBClaimLogPaid(
                        $transactionLog->first(),
                        $psb_order_id,
                        $paymentMethod
                    );
                } else {
                    $path = storage_path('logs/failed_sbp_payments.log');
                    $text = date('Y-m-d H:i:s').' | request: '. $_SERVER['REQUEST_URI']. " | " .
                        $request->input('QR_ID');

                    file_put_contents($path, PHP_EOL . $text, FILE_APPEND);
                }
            } else {
                if ($request->input('RESULT') == 0) {
                    $data = [
                        "accountNumber" => $account_number,
                        "amount" => $amount,
                        "paymentId" => $psb_order_id,
                        "paymentDateTime" => $created_at
                    ];

                    $paymentService->makeTransactionPSBLogPaid($transactionLog->first(), $data, $paymentMethod);

                    logger()->debug('PaymentController->callback: completed');
                } else {
                    $path = storage_path('logs/failed_sbp_payments.log');
                    $text = date('Y-m-d H:i:s').' | request: '. $_SERVER['REQUEST_URI']. " | " .
                        $request->input('QR_ID');

                    file_put_contents($path, PHP_EOL . $text, FILE_APPEND);
                }
            }
        } catch (\Throwable $throwable) {
            $path = storage_path('logs/failed_sbp_payments.log');
            $text = date('Y-m-d H:i:s').' | request: '. $_SERVER['REQUEST_URI']. " | " .
                $request->input('QR_ID');

            file_put_contents($path, PHP_EOL . $text, FILE_APPEND);
        }

        return $this->response()->setStatusCode(200);
    }

    private function getLogger()
    {
        $dateString = now()->format('d_m_Y');
        $filePath = 'sbp_payments_' . $dateString . '.log';
        $dateFormat = "m/d/Y H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler(storage_path('logs/' . $filePath), Logger::DEBUG);
        umask(0002);
        $stream->setFormatter($formatter);
        $processId = Str::random(5);
        $logger = new Logger($processId);
        $logger->pushHandler($stream);

        return $logger;
    }
}
