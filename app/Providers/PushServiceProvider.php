<?php

namespace App\Providers;

use App\Services\PushNotifier\FirebasePushNotifier;
use App\Services\PushNotifier\LogPushNotifier;
use App\Services\PushNotifier\NullPushNotifier;
use App\Services\PushNotifier\PushNotifier;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Psr\Log\LoggerInterface;
use http\Exception\InvalidArgumentException;

/**
 * Class PushServiceProvider
 *
 * @package App\Providers
 */
class PushServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PushNotifier::class, function () {
            return $this->createNotifier();
        });
    }

    private function createNotifier(): PushNotifier
    {
        $name = $this->config('notifier');
        switch ($name) {
            case 'none':
                return new NullPushNotifier();
            case 'log':
                return new LogPushNotifier($this->app->get(LoggerInterface::class));
            case 'firebase':
                $accountJson = file_get_contents($this->config('notifiers.firebase.account'));
                $messaging = (new Factory())->withServiceAccount($accountJson)->createMessaging();
                return new FirebasePushNotifier($messaging);
            default:
                throw new InvalidArgumentException("Unknown push notifier [{$name}].");
        }
    }

    private function config(string $key, mixed $default = null): mixed
    {
        return config("push.$key", $default);
    }
}
