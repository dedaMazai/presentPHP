<?php

namespace App\Providers;

use App\Services\PSB\PSBClient;
use Illuminate\Support\ServiceProvider;

class SBPServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(PSBClient::class, function () {
            $data['terminal'] = $this->config('terminal');
            $data['merchant'] = $this->config('merchant');
            $data['key'] = $this->config('key');
            $data['second_key'] = $this->config('second_key');

            return new PSBClient($this->config('base_uri'), $data);
        });
    }

    public function provides(): array
    {
        return [PSBClient::class];
    }

    private function config(string $key, mixed $default = null): mixed
    {
        return config("sbp.$key", $default);
    }
}
