<?php

namespace App\Auth\ThrottlesAuth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Trait ThrottlesAuth
 *
 * @package App\Auth\ThrottlesAuth
 */
trait ThrottlesAuth
{
    /**
     * @param Request  $request
     * @param AuthCase $type
     *
     * @return void
     * @throws ValidationException
     */
    protected function assertLimitOfAuthAttempts(Request $request, AuthCase $type): void
    {
        if ($this->hasTooManyAuthAttempts($request, $type)) {
            $this->sendMail($request);
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse();
        }
    }

    protected function hasTooManyAuthAttempts(Request $request, AuthCase $type): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request, $type),
            $this->maxAuthAttempts()
        );
    }

    protected function incrementAuthAttempts(Request $request, AuthCase $type): void
    {
        $this->limiter()->hit(
            $this->throttleKey($request, $type),
            $this->decayMinutes() * 60
        );
    }

    /**
     * @return void
     * @throws ValidationException
     */
    protected function sendLockoutResponse(): void
    {
        throw ValidationException::withMessages([
            $this->username() => ['Too many attempts.'],
        ])->status(429);
    }

    protected function clearAuthAttempts(Request $request, AuthCase $type): void
    {
        $this->limiter()->clear($this->throttleKey($request, $type));
    }

    protected function fireLockoutEvent(Request $request): void
    {
        event(new Lockout($request));
    }

    protected function throttleKey(Request $request, AuthCase $type): string
    {
        return Str::lower($request->input($this->username())) . '|' . $request->ip() . '|' . $type->value;
    }

    protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }

    protected function maxAuthAttempts(): int
    {
        return property_exists($this, 'maxAuthAttempts') ? $this->maxAuthAttempts : 3;
    }

    protected function decayMinutes(): int
    {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
    }

    private function sendMail(Request $request)
    {
        Mail::html(
            "<strong>Too many attemps: $request->phone</strong>",
            fn($mail) => $mail->to(['malov121998@list.ru', 'maksim.pavlov@ramax.ru'])
                ->subject('Превышено количество попыток входа')
        );


        $this->getLogger()->info(json_encode([
            'phone' => $request->phone,
        ]));
    }

    private function getLogger()
    {
        $dateString = now()->format('m_d_Y');
        $filePath = 'blocked_users_' . $dateString . '.log';
        $dateFormat = "m/d/Y H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler(storage_path('logs/' . $filePath), Logger::DEBUG);
        $stream->setFormatter($formatter);
        $processId = Str::random(5);
        $logger = new Logger($processId);
        $logger->pushHandler($stream);

        return $logger;
    }
}
