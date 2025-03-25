<?php

namespace App\Services\Payment;

use App\Jobs\CrmCompleteClaimPayJob;
use App\Jobs\CrmRefillAccountBalanceJob;
use App\Jobs\CrmSetBookingPaidJob;
use App\Jobs\UpdatePaidStatusJob;
use App\Models\Sales\Deal;
use App\Models\TransactionLog\TransactionLog;
use App\Models\TransactionLog\TransactionLogStatus;
use App\Services\Account\AccountService;
use App\Services\Account\Dto\RefillBalanceDto;
use App\Services\Apple\AppleClient;
use App\Services\Claim\ClaimRepository;
use App\Services\Claim\ClaimService;
use App\Services\Claim\Dto\SetClaimPaidDto;
use App\Services\DynamicsCrm\Exceptions\BadRequestException as DynamicsCrmBadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Payment\Dto\CreateBookingPaymentDto;
use App\Services\Payment\Dto\CreatePaymentDto;
use App\Services\Payment\Exceptions\BadRequestException;
use App\Services\Sales\Demand\DemandService;
use App\Services\Sberbank\SberbankClient;
use App\Services\TransactionLog\TransactionLogService;

/**
 * Class PaymentService
 *
 * @package App\Services\Payment
 */
class PaymentService
{
    public function __construct(
        private SberbankClient $sberbankClient,
        private AppleClient $appleClient,
        private ClaimRepository $claimRepository,
        private TransactionLogService $transactionLogService,
    ) {
    }

    /**
     * @throws BadRequestException
     */
    public function createPayment(TransactionLog $transactionLog, CreatePaymentDto $dto): string
    {
        $data = $this->sberbankClient->createPayment($transactionLog, $dto);

        if (isset($data['errorCode'])) {
            $transactionLog->update(['status' => TransactionLogStatus::failed()]);

            throw new BadRequestException(json_encode($data));
        }

        $transactionLog->update([
            'remote_order_id' => $data['orderId'],
            'status' => TransactionLogStatus::registered(),
        ]);

        return $data['formUrl'];
    }

    /**
     * @throws BadRequestException
     */
    public function createBookingPayment(CreateBookingPaymentDto $dto): array
    {
        $data = $this->sberbankClient->createBookingPayment($dto);

        if (isset($data["errorCode"]) && (int)$data["errorCode"] > 0) {
            throw new BadRequestException($data["errorMessage"], $data["errorCode"]);
        }

        return $data;
    }

    /**
     * @throws BadRequestException
     */
    public function createPaymentByApplePay(
        TransactionLog $transactionLog,
        CreatePaymentDto $dto,
        string $tokenData
    ): void {
        $data = $this->sberbankClient->createPaymentByApplePay($transactionLog, $dto, $tokenData);

        if (isset($data['error'])) {
            $transactionLog->update(['status' => TransactionLogStatus::failed()]);

            throw new BadRequestException();
        }

        $transactionLog->update([
            'remote_order_id' => $data['data']['orderId'],
            'status' => TransactionLogStatus::registered()
        ]);
    }

    public function validateApplePay(string $validateUrl): array
    {
        $data = $this->appleClient->validateRequest($validateUrl);

        return $data['paymentData'];
    }

    /**
     * @throws NotFoundException
     * @throws DynamicsCrmBadRequestException
     */
    public function makeBookingPaid(int $dealId): void
    {
        /** @var Deal $deal */
        $deal = Deal::bookingNotPaid()->where(['id' => $dealId])->first();

        if ($deal) {
            CrmSetBookingPaidJob::dispatch($deal)->onQueue('booking_payments');
        }
    }

    public function makeTransactionPSBLogPaid(TransactionLog $transactionLog, array $data, string $paymentMethod): void
    {
        $this->transactionLogService->updateStatus($transactionLog, TransactionLogStatus::paid());

        $dto = new RefillBalanceDto(
            accountNumber: $data['accountNumber'],
            user: $transactionLog->user,
            amount: $data['amount']*100,
            paymentId: $data['paymentId'],
            paymentDateTime: $transactionLog->created_at
        );

        CrmRefillAccountBalanceJob::dispatch($dto, $paymentMethod)->onQueue('refill_account_payments');
    }

    public function makeTransactionPSBClaimLogPaid(
        TransactionLog $transactionLog,
        string $paymentId,
        string $paymentMethod
    ): void {
        $this->transactionLogService->updateStatus($transactionLog, TransactionLogStatus::paid());

        $claim = $this->claimRepository->getOneById($transactionLog->claim_id);

        $dto = new SetClaimPaidDto(
            claim: $claim,
            paymentId: $paymentId,
            paymentDateTime: $transactionLog->created_at,
        );

        CrmCompleteClaimPayJob::dispatch($dto, $paymentMethod)->onQueue('claim_payments');
        UpdatePaidStatusJob::dispatch($dto)->onQueue('claim_payments');
    }

    public function makeTransactionLogPaid(TransactionLog $transactionLog, string $mdOrder): void
    {
        $this->transactionLogService->updateStatus($transactionLog, TransactionLogStatus::paid());

        logger()->debug('Executing makeTransactionLogPaid', [
            'transaction_log_id' => $transactionLog->id,
            'mdOrder' => $mdOrder,
            'claim_id' => $transactionLog->claim_id,
        ]);

        if ($transactionLog->claim_id) {
            $claim = $this->claimRepository->getOneById($transactionLog->claim_id);

            $dto = new SetClaimPaidDto(
                claim: $claim,
                paymentId: $mdOrder,
                paymentDateTime: $transactionLog->created_at,
            );

            logger()->debug('PaymentService->makeTransactionLogPaid: dispatching the CrmCompleteClaimPayJob', [
                'dto' => [
                    'payment_id' => $dto->paymentId,
                    'payment_date_time' => $dto->paymentDateTime->toDateTimeString(),
                ]
            ]);

            CrmCompleteClaimPayJob::dispatch($dto)->onQueue('claim_payments');
        } else {
            $dto = new RefillBalanceDto(
                accountNumber: $transactionLog->account_number,
                user: $transactionLog->user,
                amount: $transactionLog->amount,
                paymentId: $mdOrder,
                paymentDateTime: $transactionLog->created_at
            );

            logger()->debug('PaymentService->makeTransactionLogPaid: dispatching the CrmRefillAccountBalanceJob', [
                'dto' => [
                    'payment_id' => $dto->paymentId,
                    'payment_date_time' => $dto->paymentDateTime->toDateTimeString(),
                ]
            ]);

            CrmRefillAccountBalanceJob::dispatch($dto)->onQueue('refill_account_payments');
        }
    }
}
