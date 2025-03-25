<?php

namespace App\Providers;

use App\Services\Sberbank\ChecksumValidator;
use App\Services\Sberbank\SberbankClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

/**
 * Class SberbankServiceProvider
 *
 * @package App\Providers
 */
class SberbankServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(SberbankClient::class, function () {
            $credentialsFile = $this->config('credentials');
            $salesSellerId = $this->config('sales_seller_id');
            $booking = $this->config('sber_booking');
            if ($credentialsFile && file_exists($credentialsFile) && $salesSellerId) {
                $credentials = json_decode(file_get_contents($credentialsFile), true);
            } else {
                throw new RuntimeException();
            }

            return new SberbankClient($this->config('base_uri'), $credentials, $salesSellerId, $booking);
        });

        $this->app->singleton(ChecksumValidator::class, function () {
            $certsPath = $this->config('certs_path');
            $salesSellerId = $this->config('sales_seller_id');
            if ($certsPath && is_dir($certsPath) && $salesSellerId) {
                return new ChecksumValidator(
                    $certsPath,
                    $salesSellerId,
                );
            } else {
                throw new RuntimeException();
            }
        });
    }

    public function provides(): array
    {
        return [SberbankClient::class, ChecksumValidator::class];
    }

    private function config(string $key, mixed $default = null): mixed
    {
        return config("sberbank.$key", $default);
    }
}
