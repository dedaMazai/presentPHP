<?php

namespace App\Services\TransactionLog;

use App\Models\TransactionLog\TransactionLog;
use App\Models\TransactionLog\TransactionLogStatus;
use App\Models\PaymentMethodType;
use App\Services\TransactionLog\Dto\SaveTransactionSBPLogDto;

/**
 * Class TransactionLogService
 *
 * @package App\Services\TransactionLog
 */
class TransactionSBPLogService
{
    public function store(SaveTransactionSBPLogDto $dto): TransactionLog
    {
        return TransactionLog::create([
            'user_id' => $dto->user->id,
            'account_number' => $dto->accountNumber,
            'payment_method_type' => PaymentMethodType::card(),
            'title' => 'Пополнение лицевого счета',
            'amount' => $dto->amount,
            'status' => TransactionLogStatus::new(),
            'account_service_seller_id' => $dto->accountServiceSellerId,
            'psb_order_id' => $dto->psb_order_id
        ]);
    }

    public function updateStatus(TransactionLog $transactionLog, TransactionLogStatus $status): void
    {
        $transactionLog->update(['status' => $status]);
    }
}
