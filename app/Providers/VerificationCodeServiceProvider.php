<?php

namespace App\Providers;

use App\Auth\VerificationCode\Generation\FixedVerificationCodeGenerator;
use App\Auth\VerificationCode\Generation\NumericalVerificationCodeGenerator;
use App\Auth\VerificationCode\Generation\VerificationCodeGenerator;
use App\Auth\VerificationCode\VerificationCodeManager;
use App\Auth\VerificationCode\VerificationCodeRepository;
use App\Http\Middleware\VerificationCode\LimitVerificationCodeSending;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

/**
 * Class VerificationCodeServiceProvider
 *
 * @package App\Providers
 */
class VerificationCodeServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->registerRepository();
        $this->registerCodeGenerator();
        $this->registerManager();
        $this->registerMiddlewares();
    }

    private function registerManager(): void
    {
        $this->app->singleton(VerificationCodeManager::class, function () {
            return new VerificationCodeManager(
                $this->app->make(VerificationCodeRepository::class),
                $this->app->make(VerificationCodeGenerator::class)
            );
        });
    }

    private function registerRepository(): void
    {
        $this->app->singleton(VerificationCodeRepository::class, function () {
            return new VerificationCodeRepository(
                $this->app->make('cache.store'),
                $this->config('expiry')
            );
        });
    }

    private function registerCodeGenerator(): void
    {
        $this->app->singleton(VerificationCodeGenerator::class, function () {
            return $this->createCodeGenerator();
        });
    }

    private function registerMiddlewares(): void
    {
        $this->app->singleton(LimitVerificationCodeSending::class, function () {
            return new LimitVerificationCodeSending(
                $this->app->make(RateLimiter::class),
                $this->config('daily_limit')
            );
        });
    }

    /**
     * @return VerificationCodeGenerator
     */
    private function createCodeGenerator(): VerificationCodeGenerator
    {
        $name = $this->config('generator');

        switch ($name) {
            case 'numerical':
                return $this->createNumericalCodeGenerator($this->config('generators.numerical'));
            case 'fixed':
                return $this->createFixedCodeGenerator($this->config('generators.fixed'));
            default:
                throw new InvalidArgumentException("Unknown verification code generator [{$name}].");
        }
    }

    private function createNumericalCodeGenerator(array $config): VerificationCodeGenerator
    {
        return new NumericalVerificationCodeGenerator($config['length']);
    }

    private function createFixedCodeGenerator(array $config): VerificationCodeGenerator
    {
        return new FixedVerificationCodeGenerator($config['code']);
    }

    private function config(string $key, mixed $default = null): mixed
    {
        return config("verification_code.$key", $default);
    }
}
