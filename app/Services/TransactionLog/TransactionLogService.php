<?php

namespace App\Services\TransactionLog;

use App\Models\TransactionLog\TransactionLog;
use App\Models\TransactionLog\TransactionLogStatus;
use App\Services\TransactionLog\Dto\SaveTransactionLogDto;

/**
 * Class TransactionLogService
 *
 * @package App\Services\TransactionLog
 */
class TransactionLogService
{
    public function store(SaveTransactionLogDto $dto): TransactionLog
    {
        return TransactionLog::create([
            'user_id' => $dto->user->id,
            'account_number' => $dto->accountNumber,
            'payment_method_type' => $dto->paymentMethodType,
            'title' => $dto->title,
            'subtitle' => $dto->subtitle,
            'amount' => $dto->amount,
            'status' => $dto->status,
            'account_service_seller_id' => $dto->accountServiceSellerId,
            'claim_id' => $dto->claim?->getId(),
            'claim_number' => $dto->claim?->getNumber(),
            'claim_category_name' => $dto->claim?->getServices()[0]?->getCatalogueItemParentName() ?? null,
        ]);
    }

    public function updateStatus(TransactionLog $transactionLog, TransactionLogStatus $status): void
    {
        $transactionLog->update(['status' => $status]);
    }
}
