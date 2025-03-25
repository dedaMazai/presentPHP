<?php

namespace App\Services\PSB\Dto;

use App\Models\PaymentMethodType;

/**
 * Class CreatePaymentDto
 *
 * @package App\Services\Payment\Dto
 */
class CreatePaymentDto
{
    /**
     * @param PaymentMethodType $type
     * @param int               $amount
     * @param string            $returnUrl
     * @param string            $failUrl
     * @param string            $firstName
     * @param string            $lastName
     * @param string            $middleName
     * @param string            $email
     */
    public function __construct(
        public PaymentMethodType $type,
        public int $amount,
        public array $items,
        public string $currency,
        public string $order,
        public string $terminal,
        public string $desc,
        public string $trtype,
        public string $merch_name,
        public string $merchant,
        public string $nonce,
        public ?string $firstName,
        public ?string $lastName,
        public ?string $middleName,
        public string $backref,
        public string $email,
        public string $claim_id,
        public string $p_sign
    ) {
    }
}
