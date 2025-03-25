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
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class UpdatePaidStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $attemptsToNotify;
    private array|string|null $email;
    private int $waitTime = 90;
    private int $maxAttempts = 4;

    public function __construct(private SetClaimPaidDto $claimPaidDto)
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
        $attempts = $this->attempts();

        try {
            /** @var ClaimRepository $claimRepository */
            $claimRepository = app(ClaimRepository::class);
            $claimService->updatePaidStatus($this->claimPaidDto->claim->getId());
            $claimRepository->updateClaimInCache($this->claimPaidDto->claim->getId());
        } catch (Throwable $e) {
            $attempts = $this->attempts();
            $delay = pow(2, $attempts) * 90;

            if ($attempts > $this->maxAttempts) {
                logger()->error('Достигнуто максимальное количество попыток оплаты счета в CRM', [
                    'message' => $e->getMessage(),
                    'dto' => $this->claimPaidDto,
                ]);

                $this->fail();
            } else {
                $this->release($delay);
            }
        }
    }

    public function uniqueId(): int
    {
        return $this->claimPaidDto->paymentId;
    }
}
