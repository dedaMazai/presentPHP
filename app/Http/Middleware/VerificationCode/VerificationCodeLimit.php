<?php

namespace App\Http\Middleware\VerificationCode;

use App\Auth\VerificationCode\VerificationCase;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;

/**
 * Class VerificationCodeLimit
 *
 * @package App\Http\Middleware\VerificationCode
 */
class VerificationCodeLimit
{
    use InteractsWithTime;

    private string $key;

    private bool $isLocked = false;

    public function __construct(
        private string $name,
        private int $maxAttempts,
        private int $decaySeconds,
        private string $phone,
        private RateLimiter $limiter
    ) {
        $this->key = 'verification_code.' . Str::lower($name) . ".{$phone}";
    }

    public static function daily(
        string $phone,
        RateLimiter $limiter,
        int $maxAttempts = 3
    ): self {
        return new self(
            name: "daily",
            maxAttempts: $maxAttempts,
            decaySeconds: 60 * 60 * 24, // seconds * minutes * hours
            phone: $phone,
            limiter: $limiter
        );
    }

    public static function minute(
        VerificationCase $case,
        string $phone,
        RateLimiter $limiter,
        int $maxAttempts = 1
    ): self {
        return new self(
            name: "minute.{$case->value}",
            maxAttempts: $maxAttempts,
            decaySeconds: 60,
            phone: $phone,
            limiter: $limiter
        );
    }

    public function tooManyAttempts(): bool
    {
        $this->isLocked = $this->limiter->tooManyAttempts($this->key, $this->maxAttempts);

        return $this->isLocked;
    }

    public function hit(): void
    {
        $this->limiter->hit($this->key, $this->decaySeconds);
    }

    public function getHeaders(): array
    {
        $timer = $this->getTimer();
        $name = Str::ucfirst($this->name);

        $headers = [
            "X-RateLimit-VerificationCode-{$name}-Limit" => $this->maxAttempts,
            "X-RateLimit-VerificationCode-{$name}-Remaining" => $this->calculateRemainingAttempts(),
            "X-RateLimit-VerificationCode-{$name}-Timer" => $timer,
        ];

        if ($this->isLocked) {
            $headers["X-RateLimit-VerificationCode-{$name}-Retry-After"] = $timer;
            $headers["X-RateLimit-VerificationCode-{$name}-Reset"] = $this->availableAt($timer);
        }

        return $headers;
    }

    private function getTimer(): int
    {
        $timer = $this->limiter->availableIn($this->key);
        if ($timer < 0) {
            return 0;
        }

        return $timer;
    }

    private function calculateRemainingAttempts(): int
    {
        if ($this->isLocked) {
            return 0;
        }

        return $this->limiter->retriesLeft($this->key, $this->maxAttempts);
    }
}
