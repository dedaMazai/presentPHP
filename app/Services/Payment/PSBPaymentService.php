<?php

namespace App\Services\Payment;

use App\Models\Claim\Claim;
use App\Models\Claim\ClaimCatalogue\ClaimCatalogueItemSellerType;
use App\Models\TransactionLog\TransactionLog;
use App\Models\TransactionLog\TransactionLogStatus;
use App\Models\User\User;
use App\Services\Account\AccountRepository;
use App\Services\Claim\ClaimRepository;
use App\Services\Claim\ClaimService;
use App\Services\Payment\Exceptions\BadRequestException;
use App\Services\PSB\Dto\CreatePaymentDto;
use App\Services\PSB\PSBClient;
use App\Services\TransactionLog\Dto\SaveClaimTransactionPSBLogDto;
use App\Services\TransactionLog\Dto\SaveTransactionPSBLogDto;
use App\Services\TransactionLog\TransactionPSBClaimLogService;
use App\Services\TransactionLog\TransactionPSBLogService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PaymentService
 *
 * @package App\Services\Payment
 */
class PSBPaymentService
{
    public function __construct(
        private PSBClient $psbClient,
        private ClaimRepository $claimRepository,
        private ClaimService $claimService,
        private TransactionPSBLogService $transactionLogService,
        private TransactionPSBClaimLogService $transactionPSBClaimLogService,
        private AccountRepository $accountRepository
    ) {
    }

    public function createPayment(TransactionLog $transactionLog, array $paymentItem)
    {
        $data = $this->psbClient->createPayment($paymentItem);

        if (isset($data['errorCode'])) {
            $transactionLog->update(['status' => TransactionLogStatus::failed()]);

            throw new BadRequestException(json_encode($data));
        }

        return $data;
    }


    public function generateSignKey($data)
    {
        $vars = ["amount","currency","order","merch_name","merchant","terminal","email","trtype",
            "timestamp","nonce","backref"];
        $string= '';

        foreach ($vars as $param) {
            if (isset($data[$param]) && strlen($data[$param]) != 0) {
                $string.= strlen($data[$param]) . $data[$param];
            } else {
                $string .= "-";
            }
        }

        $key = strtoupper(implode(unpack("H32", pack("H32", config('psb.key'))
            ^ pack("H32", config('psb.second_key')))));


        return strtoupper(hash_hmac('sha256', $string, pack('H*', $key)));
    }

    public function generateClaimSignKey($data, $merchant)
    {
        $vars = ["amount","currency","terminal","trtype","backref","order"];
        $string= '';

        foreach ($vars as $param) {
            if (isset($data[$param]) && strlen($data[$param]) != 0) {
                $string.= strlen($data[$param]) . $data[$param];
            } else {
                $string .= "-";
            }
        }

        $key = strtoupper(implode(unpack("H32", pack("H32", $merchant['first_key'])
            ^ pack("H32", $merchant['second_key']))));


        return strtoupper(hash_hmac('sha256', $string, pack('H*', $key)));
    }

    public function findClaim(string $id): Claim
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

    public function createTransactionLog(
        string $accountNumber,
        Request $request,
        User $user,
        $psb_order_id,
        $account_seller_id
    ): TransactionLog {
        $transactionLogDto = new SaveTransactionPSBLogDto(
            user: $user,
            accountNumber: $accountNumber,
            title: 'Пополнение лицевого счета',
            amount: floor($request->input('amount'))/100,
            status: TransactionLogStatus::new(),
            accountServiceSellerId: $account_seller_id,
            psb_order_id: $psb_order_id
        );

        return $this->transactionLogService->store($transactionLogDto);
    }

    public function createPaymentItem(User $user, Request $request, $accountNumber, $psbOrderId, $merchant)
    {
        $account = $this->accountRepository->getAccountByNumber($accountNumber);

        if ($accounts = $request->input('accounts')) {
            foreach ($accounts as $account) {
                $result[$account['number']] = $account['amount'];
            }

            $addinfo = json_encode($result);
        } elseif ($account != null) {
            $addinfo = "$accountNumber: ". $account->getAddress();
        } else {
            $addinfo = "Пополнение лицевого счёта $psbOrderId";
        }

        $data  = [
            'amount' => floor($request->input('amount'))/100,
            'currency' => 'RUB',
            'order' =>  $psbOrderId,
            'desc'  => "Пополнение лицевого счёта $accountNumber",
            'terminal' => $merchant['merchant'],
            'trtype' => '1',
            'email' => $user->email,
            'backref' => url('/balance/success'),
            'addinfo' => $addinfo,
            'notify_url' => url('api/v1/accounts/'.$accountNumber.'/psb/callback'),
            'merchant_notify_email' => 'pioneertest@mail.test',
        ];

        $data['p_sign'] = $this->generateClaimSignKey($data, $merchant);

        return $data;
    }

    public function createClaimPaymentItem(User $user, $claim, $accountNumber, $psbOrderId, $merchant)
    {
        $account = $this->accountRepository->getAccountByNumber($accountNumber);

        if ($account != null) {
            $addinfo = "$accountNumber: ". $account->getAddress();
        } else {
            $addinfo = "Оплата заявки $psbOrderId";
        }

        $data  = [
            'amount' => floor($claim->getTotalPayment())/100,
            'currency' => 'RUB',
            'order' =>  $psbOrderId,
            'desc'  => "Оплата заявки $psbOrderId",
            'terminal' => $merchant['merchant'],
            'trtype' => '1',
            'email' => $user->email,
            'backref' => url('/payment/checkout-success'),
            'addinfo' => $addinfo,
            'notify_url' => url('api/v1/accounts/'.$accountNumber.'/psb/callback'),
            'merchant_notify_email' => 'pioneertest@mail.test',
        ];

        $data['p_sign'] = $this->generateClaimSignKey($data, $merchant);

        return $data;
    }

    public function createTransactionLogWithClaim(
        string $accountNumber,
        User $user,
        ?Claim $claim,
        string $psb_order_id
    ): TransactionLog {
        $transactionLogDto = new SaveClaimTransactionPSBLogDto(
            user: $user,
            accountNumber: $accountNumber,
            title: 'Оплата заявки №' . $claim->getNumber(),
            subtitle: $claim?->getTheme()->label,
            amount: floor($claim->getTotalPayment())/100,
            status: TransactionLogStatus::new(),
            accountServiceSellerId: $this->claimService->getSellerForClaim($claim),
            claim: $claim,
            psb_order_id: $psb_order_id
        );

        return $this->transactionPSBClaimLogService->store($transactionLogDto);
    }

    public function getClaimCredetionals(?Claim $claim)
    {
        $credetionals = json_decode(file_get_contents(config('psb.free_certs_path')), true);

        return $credetionals[$this->claimService->getSellerForClaim($claim)];
    }

    public function getCredetionals($accountId)
    {
        $account_crm_id = $this->accountRepository->getAccountByNumber($accountId)->getServiceSeller()->getId();

        $credetionals = json_decode(file_get_contents(config('psb.certs_path')), true);

        return ['credetionals' => $credetionals[$account_crm_id], 'account_seller_id' => $account_crm_id];
    }
}
