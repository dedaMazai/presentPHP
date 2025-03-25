<?php

namespace App\Auth\VerificationCode;

use App\Auth\VerificationCode\Generation\VerificationCodeGenerator;
use App\Auth\VerificationCode\Notification\VerificationCodeNotification;
use Illuminate\Notifications\AnonymousNotifiable;

/**
 * Class VerificationCodeManager
 *
 * @package App\Auth\VerificationCode
 */
class VerificationCodeManager
{
    public function __construct(
        private VerificationCodeRepository $repository,
        private VerificationCodeGenerator $generator,
    ) {
    }

    public function send(VerificationCase $case, string $phone): void
    {
        $code = $this->generator->generate();
        $this->repository->remember($case, $phone, $code);
        $this->notify($phone, $code);
    }

    /**
     * @param VerificationCase $case
     * @param string           $phone
     * @param VerificationCode $code
     *
     * @return void
     * @throws WrongVerificationCodeException
     */
    public function verify(VerificationCase $case, string $phone, VerificationCode $code): void
    {
        $storedCode = $this->repository->find($case, $phone);

        if ($storedCode === null || !$code->equals($storedCode)) {
            throw new WrongVerificationCodeException();
        }
    }

    public function forget(VerificationCase $case, string $phone): void
    {
        $this->repository->forget($case, $phone);
    }

    private function notify(string $phone, VerificationCode $code): void
    {
        $notifiable = new AnonymousNotifiable();
        $notifiable->route('sms', $phone);
        $notifiable->notify(new VerificationCodeNotification($code));
    }
}
