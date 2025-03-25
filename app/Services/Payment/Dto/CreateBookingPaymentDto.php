<?php

namespace App\Services\Payment\Dto;

use App\Models\PaymentMethodType;
use App\Models\Sales\Demand\Demand;
use App\Models\User\User;

/**
 * Class CreateBookingPaymentDto
 *
 * @package App\Services\Payment\Dto
 */
class CreateBookingPaymentDto
{
    /**
     * @param PaymentMethodType $type
     * @param int $amount
     * @param Demand $demand
     * @param User $user
     */
    public function __construct(
        public PaymentMethodType $type,
        public int $amount,
        public Demand $demand,
        public User $user
    ) {
    }
}
