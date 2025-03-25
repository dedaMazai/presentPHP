<?php

namespace App\Mail;

use App\Services\Claim\Dto\SetClaimPaidDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmFailCompletingClaimPayMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private SetClaimPaidDto $dto)
    {
    }

    public function build()
    {
        return $this->view('emails.crm_failed_completing_claim_pay', [
            'claim_id' => $this->dto->claim->getId(),
            'user_id' => $this->dto->claim->getUser()?->id,
            'user_crm_id' => $this->dto->claim->getUser()?->crm_id,
            'payment_id' => $this->dto->paymentId,
            'payment_date' => $this->dto->paymentDateTime->toDateString(),
        ]);
    }
}
