<?php

namespace App\Jobs;

use App\Mail\CrmFailCompletingClaimPayMail;
use App\Services\Claim\ClaimRepository;
use App\Services\Claim\ClaimService;
use App\Services\Claim\Dto\SetClaimPaidDto;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Math\MathService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CrmCompleteClaimPayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $attemptsToNotify;
    private array|string|null $email;

    public function __construct(private SetClaimPaidDto $claimPaidDto, private string $paymentMethod)
    {
        $this->attemptsToNotify = (int)config('payments_notification.failed_attempts_before_notify');
        $this->email = config('payments_notification.email');
    }

    /**
     * @throws Throwable
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function handle(ClaimService $claimService)
    {
        try {
            /** @var ClaimRepository $claimRepository */
            $claimService->setPaid($this->claimPaidDto->claim->getUser(), $this->claimPaidDto, $this->paymentMethod);
        } catch (Throwable $e) {
            $this->release(MathService::fibonacci($this->attempts()));

            logger()->error('An error occurred when set claim to paid status in the CRM', [
                'message' => $e->getMessage(),
                'dto' => $this->claimPaidDto,
            ]);

            if ($this->attempts() >= $this->attemptsToNotify && $this->email) {
                Mail::to($this->email)->send(new CrmFailCompletingClaimPayMail($this->claimPaidDto));
            }

            throw $e;
        }
    }

    public function uniqueId(): int
    {
        return $this->claimPaidDto->paymentId;
    }
}
