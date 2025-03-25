<?php

namespace App\Services\Payment\Dto;

/**
 * Class PaymentItemDto
 *
 * @package App\Services\Payment\Dto
 */
class PaymentItemDto
{
    public function __construct(
        public int $positionId,
        public string $name,
        public int $quantity,
        public string $itemCode,
        public ?int $itemPrice,
    ) {
    }
}
