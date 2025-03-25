<?php

namespace App\Services\TransactionLog\Dto;

use App\Models\Claim\Claim;
use App\Models\PaymentMethodType;
use App\Models\TransactionLog\TransactionLogStatus;
use App\Models\User\User;

/**
 * Class SaveTransactionLogDto
 *
 * @package App\Services\TransactionLog\Dto
 */
class SaveTransactionPSBLogDto
{
    public function __construct(
        public User $user,
        public string $accountNumber,
        public string $title,
        public float $amount,
        public TransactionLogStatus $status,
        public string $accountServiceSellerId,
        public string $psb_order_id
    ) {
    }
}
