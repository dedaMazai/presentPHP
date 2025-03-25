<?php

namespace App\Mail;

use App\Models\Sales\Deal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CrmFailSetBookingPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private Deal $deal)
    {
    }

    public function build()
    {
        return $this->view('emails.crm_failed_booking_paid', [
            'demand_id' => $this->deal->demand_id,
            'user_id' => $this->deal->user->id,
        ]);
    }
}
