<?php

namespace App\Services\Payment\Dto;

use App\Models\TransactionLog\TransactionLog;

/**
 * Class ValidatePaymentChecksumDto
 *
 * @package App\Services\Payment\Dto
 */
class ValidatePaymentChecksumDto
{
    public function __construct(
        public ?TransactionLog $transactionLog,
        public string $checksum,
        public array $checkParams,
    ) {
    }
}
