<?php

namespace App\Http\Middleware\VerificationCode;

use App\Auth\VerificationCode\VerificationCase;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class LimitVerificationCodeSending
 *
 * @package App\Http\Middleware\VerificationCode
 */
class LimitVerificationCodeSending
{
    public function __construct(
        private RateLimiter $limiter,
        private int $dailyMaxAttempts
    ) {
    }

    public function handle(Request $request, Closure $next, string $phoneKey, string $caseKey): Response
    {
        $phone = $request->input($phoneKey);
        $case = VerificationCase::tryFrom($request->input($caseKey) ?? '');
        if (empty($phone) || empty($case)) {
            return $next($request);
        }

        $limits = $this->createLimits($case, $phone);

        foreach ($limits as $limit) {
            if ($limit->tooManyAttempts()) {
                throw $this->buildException($limits);
            }
        }

        /** @var Response $response */
        $response = $next($request);

        if ($response->isSuccessful()) {
            foreach ($limits as $limit) {
                $limit->hit();
            }
        }

        $response->headers->add($this->getHeaders($limits));

        return $response;
    }

    /**
     * @param VerificationCase $case
     * @param string           $phone
     *
     * @return VerificationCodeLimit[]
     */
    private function createLimits(VerificationCase $case, string $phone): array
    {
        return [
            VerificationCodeLimit::daily($phone, $this->limiter, $this->dailyMaxAttempts),
            VerificationCodeLimit::minute($case, $phone, $this->limiter),
        ];
    }

    /**
     * @param VerificationCodeLimit[] $limits
     *
     * @return array
     */
    private function getHeaders(array $limits): array
    {
        $headers = [];

        foreach ($limits as $limit) {
            $headers += $limit->getHeaders();
        }

        return $headers;
    }

    /**
     * @param VerificationCodeLimit[] $limits
     *
     * @return HttpException
     */
    private function buildException(array $limits): HttpException
    {
        return new HttpException(
            429,
            'Too Many Attempts.',
            null,
            $this->getHeaders($limits)
        );
    }
}
