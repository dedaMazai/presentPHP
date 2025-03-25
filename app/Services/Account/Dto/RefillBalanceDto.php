<?php

namespace App\Services\Account\Dto;

use App\Models\User\User;
use Carbon\Carbon;

class RefillBalanceDto
{
    public function __construct(
        public string $accountNumber,
        public User $user,
        public int $amount,
        public string $paymentId,
        public Carbon $paymentDateTime,
    ) {
    }
}
