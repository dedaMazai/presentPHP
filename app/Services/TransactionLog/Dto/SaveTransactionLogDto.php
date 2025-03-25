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
class SaveTransactionLogDto
{
    public function __construct(
        public User $user,
        public string $accountNumber,
        public ?PaymentMethodType $paymentMethodType,
        public string $title,
        public ?string $subtitle,
        public int $amount,
        public TransactionLogStatus $status,
        public string $accountServiceSellerId,
        public ?Claim $claim,
    ) {
    }
}
