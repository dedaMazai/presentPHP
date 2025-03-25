<?php

namespace App\Mail;

use App\Services\Account\Dto\RefillBalanceDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmFailCompletingRefillAccountBalanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private RefillBalanceDto $dto)
    {
    }

    public function build()
    {
        return $this->view('emails.crm_failed_completing_refill_account_balance', [
            'claim_id' => $this->dto->accountNumber,
            'amount' => $this->dto->amount,
            'user_id' => $this->dto->user->id,
            'payment_id' => $this->dto->paymentId,
            'payment_date' => $this->dto->paymentDateTime->toDateString(),
        ]);
    }
}
