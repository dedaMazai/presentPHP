<?php

namespace App\Services\Payment\Dto;

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
     * @param PaymentItemDto[]  $items
     * @param string            $firstName
     * @param string            $lastName
     * @param string            $middleName
     * @param string            $email
     */
    public function __construct(
        public PaymentMethodType $type,
        public int $amount,
        public string $returnUrl,
        public string $failUrl,
        public array $items,
        public string $firstName,
        public string $lastName,
        public string $middleName,
        public string $email,
    ) {
    }
}
