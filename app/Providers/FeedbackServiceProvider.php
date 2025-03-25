<?php

namespace App\Providers;

use App\Services\DynamicsCrm\DynamicsCrmClient;
use App\Services\Feedback\FeedbackApiClient;
use App\Services\Feedback\FeedbackService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Class FeedbackServiceProvider
 *
 * @package App\Providers
 */
class FeedbackServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->app->singleton(FeedbackService::class, function () {
            return new FeedbackService(
                dynamicsCrmClient: $this->app->make(DynamicsCrmClient::class),
                feedbackApiClient: new FeedbackApiClient(config('services.feedback.base_uri')),
            );
        });
    }

    public function provides(): array
    {
        return [FeedbackService::class];
    }
}
