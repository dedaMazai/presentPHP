<?php

namespace App\Http\Api\External\V1\Controllers;

use App\Http\Api\External\V1\Requests\PaymentCallbackRequest;
use App\Models\Claim\Claim;
use App\Models\PaymentMethodType;
use App\Models\TransactionLog\SberbankOperationType;
use App\Models\TransactionLog\TransactionLog;
use App\Models\TransactionLog\TransactionLogStatus;
use App\Models\User\User;
use App\Services\Account\AccountRepository;
use App\Services\Claim\ClaimRepository;
use App\Services\Claim\ClaimService;
use App\Services\DynamicsCrm\Exceptions\BadRequestException as DynamicsCrmBadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Payment\Dto\CreatePaymentDto;
use App\Services\Payment\Dto\PaymentItemDto;
use App\Services\Payment\Dto\ValidatePaymentChecksumDto;
use App\Services\Payment\Exceptions\BadRequestException;
use App\Services\Payment\PaymentService;
use App\Services\Sberbank\ChecksumValidator;
use App\Services\TransactionLog\Dto\SaveTransactionLogDto;
use App\Services\TransactionLog\TransactionLogService;
use App\Validation\Payment\PaymentValidation;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PaymentController
 *
 * @package App\Http\Api\External\V1\Controllers
 */
class PaymentController extends Controller
{
    public function __construct(
        private TransactionLogService $transactionLogService,
        private ClaimRepository $claimRepository,
        private ClaimService $claimService,
        private AccountRepository $accountRepository
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function payByCard(
        string $accountNumber,
        Request $request,
        PaymentService $service,
    ): Response {
        $this->validate($request, [
            'amount' => 'required_without:claim_id|integer|min:1',
            'claim_id' => 'required_without:amount|string',
        ]);

        logger()->debug('PaymentController::payByCard', [
            'claim_id' => $request->input('claim_id'),
            'amount' => $request->input('amount'),
        ]);

        $claim = null;

        if ($request->input('claim_id')) {
            $claim = $this->findClaim($request->input('claim_id'));
        }

        $transactionLog = $this->createTransactionLog(
            $accountNumber,
            PaymentMethodType::card(),
            $request,
            $claim,
        );
        $paymentDto = $this->createPaymentDto($this->getAuthUser(), $transactionLog, $claim);
        $url = $service->createPayment($transactionLog, $paymentDto);

        return $this->response(['url' => $url]);
    }

    /**
     * @throws BadRequestException
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    public function payByApplePay(
        string $accountNumber,
        Request $request,
        PaymentService $service,
    ): Response {
        $this->validate($request, [
            'amount' => 'required_without:claim_id|integer',
            'claim_id' => 'required_without:amount|string',
            'token_data' => 'required',
        ]);

        $claim = null;
        if ($request->input('claim_id')) {
            $claim = $this->findClaim($request->input('claim_id'));
        }

        $transactionLog = $this->createTransactionLog(
            $accountNumber,
            PaymentMethodType::apple(),
            $request,
            $claim
        );
        $paymentDto = $this->createPaymentDto($this->getAuthUser(), $transactionLog, $claim);
        $service->createPaymentByApplePay($transactionLog, $paymentDto, $request->input('token_data'));

        return $this->empty();
    }

    /**
     * @throws ValidationException
     */
    public function validateApplePay(
        string $accountNumber,
        Request $request,
        PaymentService $service,
    ): Response {
        $this->validate($request, [
            'url' => 'required|string',
        ]);

        $paymentData = $service->validateApplePay($request->input('url'));

        return $this->response(['payment_data' => $paymentData]);
    }

    /**
     * @throws NotFoundException
     * @throws ValidationException
     * @throws DynamicsCrmBadRequestException
     */
    public function callback(
        PaymentCallbackRequest $request,
        PaymentService $paymentService,
        ChecksumValidator $checksumValidator,
    ): Response {
        logger()->debug('PaymentController->callback: Received callback request from Sber', [
            'request_data' => [
                'amount' => $request->amount,
                'mdOrder' => $request->mdOrder,
                'orderNumber' => $request->orderNumber,
                'operation' => $request->operation,
                'status' => $request->status,
            ],
        ]);
        $operationType = SberbankOperationType::from($request->input('operation'));

        $orderInfo = explode('-', $request->input('orderNumber'));
        $dealId = null;
        $transactionLog = null;
        if ($orderInfo[0] == 'deal') {
            $dealId = $orderInfo[1];
        } else {
            /** @var TransactionLog $transactionLog */
            $transactionLog = TransactionLog::find($orderInfo[1]);
        }

        if (!$dealId && !$transactionLog) {
            throw new BadRequestHttpException('Either deal or transaction should exist.');
        }

        $validatePaymentChecksumDto = new ValidatePaymentChecksumDto(
            transactionLog: $transactionLog ?? null,
            checksum: $request->input('checksum'),
            checkParams: is_array($request->query()) ? $request->query() : [],
        );

        $isValid = $checksumValidator->validate($validatePaymentChecksumDto);

        if (!$isValid) {
            throw new RuntimeException('Wrong checksum.');
        }

        logger()->debug('PaymentController->callback: checksum is valid');

        if ($operationType->equals(SberbankOperationType::deposited())) {
            if ($transactionLog) {
                if ($request->input('status') == 1) {
                    $paymentService->makeTransactionLogPaid($transactionLog, $request->input('mdOrder'));
                } else {
                    $this->transactionLogService->updateStatus($transactionLog, TransactionLogStatus::failed());
                }
            } elseif ($request->input('status') == 1) {
                $paymentService->makeBookingPaid($dealId);
            }
        }

        logger()->debug('PaymentController->callback: completed');

        return $this->empty();
    }

    /**
     * @throws AuthenticationException
     * @throws ValidationException
     * @throws InvalidArgumentException
     */
    private function createTransactionLog(
        string $accountNumber,
        PaymentMethodType $paymentMethodType,
        Request $request,
        ?Claim $claim
    ): TransactionLog {
        $transactionLogDto = new SaveTransactionLogDto(
            user: $this->getAuthUser(),
            accountNumber: $accountNumber,
            paymentMethodType: $paymentMethodType,
            title: $claim ? 'Оплата заявки №' . $claim->getNumber() ?? $claim->getId() : 'Пополнение лицевого счета',
            subtitle: $claim?->getTheme()->label,
            amount: $claim ? $claim->getTotalPayment() : $request->input('amount'),
            status: TransactionLogStatus::new(),
            accountServiceSellerId: $claim ? $this->claimService->getSellerForClaim($claim):
                $this->getAccountCrmId($accountNumber),
            claim: $claim,
        );

        return $this->transactionLogService->store($transactionLogDto);
    }

    private function createPaymentDto(User $user, TransactionLog $transactionLog, ?Claim $claim): CreatePaymentDto
    {
        $paymentItemDtos = [];
        if ($claim) {
            foreach ($claim->getServices() as $key => $serviceItem) {
                if ($serviceItem->getQuantity()) {
                    $paymentItemDtos[] = new PaymentItemDto(
                        positionId: ++$key,
                        name: $serviceItem->getCatalogueItem()->getName(),
                        quantity: $serviceItem->getQuantity(),
                        itemCode: 'NONE',
                        itemPrice: $serviceItem->getCost()
                    );
                }
            }

            $returnUrl = url(
                '/claims/' . $transactionLog->claim_id . '/payment/success?amount=' . $transactionLog->amount,
                [],
                true
            );
            $failUrl = url(
                '/claims/' . $transactionLog->claim_id . '/payment/fail',
                [],
                true
            );
        } else {
            $paymentItemDtos[] = new PaymentItemDto(
                positionId: 1,
                name: 'Пополнение лицевого счета',
                quantity: 1,
                itemCode: 'NONE',
                itemPrice: $transactionLog->amount
            );

            $returnUrl = url(
                '/apartment/' . $transactionLog->account_number . '/balance/success?amount=' . $transactionLog->amount,
                [],
                true
            );
            $failUrl = url(
                '/apartment/' . $transactionLog->account_number . '/balance/fail',
                [],
                true
            );
        }

        return new CreatePaymentDto(
            type: $transactionLog->payment_method_type,
            amount: $transactionLog->amount,
            returnUrl: $returnUrl,
            failUrl: $failUrl,
            items: $paymentItemDtos,
            firstName: $user->first_name,
            lastName: $user->last_name,
            middleName: $user->middle_name,
            email: $user->email,
        );
    }

    public function getAccountCrmId($accountId)
    {
        $account_crm_id = $this->accountRepository->getAccountByNumber($accountId)->getServiceSeller()->getId();

        return  $account_crm_id;
    }

    private function findClaim(string $id): Claim
    {
        $claim = $this->claimRepository->getOneById($id);
        if ($claim === null) {
            throw new NotFoundHttpException('Claim not found.');
        } elseif ($claim->getTotalPayment() < 0 || $claim->getTotalPayment() === null) {
            throw new BadRequestHttpException('Claim can\'t be paid.');
        } elseif ($claim->getPaymentStatus()?->isFullyPaid()) {
            throw new BadRequestHttpException('Claim already paid.');
        }

        return $claim;
    }
}
