<?php

namespace App\Services\Sales;

use App\Models\Sales\Payment;
use Carbon\Carbon;

/**
 * Class PaymentRepository
 *
 * @package App\Services\Sales
 */
class PaymentRepository
{
    public function makePayment(array $data): Payment
    {
        return new Payment(
            id: $data['id'],
            number: $data['number'],
            date: new Carbon($data['date']),
            sum: $data['sum'],
            assignment: $data['assignment'] ?? null,
        );
    }
}
