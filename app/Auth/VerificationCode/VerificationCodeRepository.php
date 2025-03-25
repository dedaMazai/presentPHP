<?php

namespace App\Auth\VerificationCode;

use Psr\SimpleCache\CacheInterface;

/**
 * Class VerificationCodeRepository
 *
 * @package App\Auth\VerificationCode
 */
class VerificationCodeRepository
{
    public function __construct(
        private CacheInterface $cache,
        private int $expiry // in seconds
    ) {
    }

    public function find(VerificationCase $case, string $phone): ?VerificationCode
    {
        $code = $this->cache->get($this->key($case, $phone));
        if ($code === null) {
            return null;
        }

        return VerificationCode::fromString($code);
    }

    public function remember(VerificationCase $case, string $phone, VerificationCode $code): void
    {
        $this->cache->set(
            $this->key($case, $phone),
            $code->toString(),
            $this->expiry
        );
    }

    public function forget(VerificationCase $case, string $phone): void
    {
        $this->cache->delete($this->key($case, $phone));
    }

    private function key(VerificationCase $case, string $phone): string
    {
        return "verification_code.{$case->value}.$phone";
    }
}
