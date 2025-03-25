<?php

namespace App\Providers;

use App\Services\Feedback\FeedbackApiClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Class FeedbackApiClientProvider
 *
 * @package App\Providers
 */
class FeedbackApiClientProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(FeedbackApiClient::class, function () {
            return new FeedbackApiClient(
                config('services.feedback.base_uri'),
            );
        });
    }

    public function provides(): array
    {
        return [FeedbackApiClient::class];
    }
}
