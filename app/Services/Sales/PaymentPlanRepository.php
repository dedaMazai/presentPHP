<?php

namespace App\Services\Sales;

use App\Models\Sales\PaymentPlan;
use Carbon\Carbon;

/**
 * Class PaymentPlanRepository
 *
 * @package App\Services\Sales
 */
class PaymentPlanRepository
{
    public function makePaymentPlan(array $data): PaymentPlan
    {
        return new PaymentPlan(
            id: $data['id'],
            number: $data['number'],
            date: new Carbon($data['date']),
            sum: $data['sum'],
            sumPayment: $data['sumPayment'] ?? null,
            sumDebt: $data['sumDebt'] ?? null,
            assignment: $data['assignment'] ?? null,
            signPay: $data['signPay'] ?? null,
            numberDaysDelay: $data['numberDaysDelay'] ?? null,
        );
    }
}
