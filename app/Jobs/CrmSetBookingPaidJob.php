<?php

namespace App\Jobs;

use App\Mail\CrmFailSetBookingPaidMail;
use App\Models\Sales\Deal;
use App\Services\Math\MathService;
use App\Services\Sales\Demand\DemandService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class CrmSetBookingPaidJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $attemptsToNotify;
    private array|string $email;

    public function __construct(private Deal $deal)
    {
        $this->attemptsToNotify = (int)config('payments_notification.failed_attempts_before_notify');
        $this->email = config('payments_notification.email');
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function handle(DemandService $demandService)
    {
        try {
            $demandService->setBookingPaid($this->deal->demand_id);
        } catch (Throwable $e) {
            $this->release(MathService::fibonacci($this->attempts()));

            logger()->error('An error occurred when set booking to paid status in the CRM', [
                'message' => $e->getMessage(),
                'deal_id' => $this->deal->id,
            ]);

            if ($this->attempts() >= $this->attemptsToNotify) {
                Mail::to($this->email)->send(new CrmFailSetBookingPaidMail($this->deal));
            }

            throw $e;
        }
    }

    public function uniqueId(): int
    {
        return $this->deal->demand_id;
    }
}
