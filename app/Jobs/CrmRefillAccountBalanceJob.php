<?php

namespace App\Jobs;

use App\Mail\CrmFailCompletingRefillAccountBalanceMail;
use App\Services\Account\AccountService;
use App\Services\Account\Dto\RefillBalanceDto;
use App\Services\Math\MathService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class CrmRefillAccountBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $attemptsToNotify;
    private array|string|null $email;
    private int $waitTime = 90;
    private int $maxAttempts = 4;

    public function __construct(private RefillBalanceDto $dto, private string $paymentMethod)
    {
        $this->attemptsToNotify = (int)config('payments_notification.failed_attempts_before_notify');
        $this->email = config('payments_notification.email');
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function handle(AccountService $accountService)
    {
        try {
            $accountService->refillBalance($this->dto, $this->paymentMethod);
        } catch (Throwable $e) {
            $attempts = $this->attempts();
            $delay = pow(2, $attempts) * 90;

            if ($attempts > $this->maxAttempts) {
                logger()->error('Достигнуто максимальное количество попыток оплаты счета в CRM', [
                    'message' => $e->getMessage(),
                    'dto' => $this->dto,
                ]);

                $this->fail();
            } else {
                $this->release($delay);
            }
        }
    }

    public function uniqueId(): int
    {
        return $this->dto->paymentId;
    }
}
