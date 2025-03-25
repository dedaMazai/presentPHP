<?php

namespace App\Services\Beeline;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Notifications\Notification;
use RuntimeException;

/**
 * Class BeelineClient
 *
 * @package App\Services\Beeline
 */
class BeelineClient
{
    private HttpClient $httpClient;

    public function __construct(
        string $baseUri,
        private string $login,
        private string $password,
        private string $sender,
    ) {
        $this->httpClient = new HttpClient([
            'base_uri' => $baseUri,
        ]);
    }

    public function send(mixed $notifiable, Notification $notification): void
    {
        $this->request(
            'POST',
            '',
            [
                RequestOptions::FORM_PARAMS => [
                    'user' => $this->login,
                    'pass' => $this->password,
                    'sender' => $this->sender,
                    'action' => 'post_sms',
                    'message' => $notification->toSms($notifiable),
                    'target' => $notifiable->routeNotificationFor('beeline'),
                ],
            ]
        );
    }

    private function request(string $method, string $uri, array $options = []): int
    {
        try {
            $response = $this->httpClient->request($method, $uri, $options);

            return $response->getStatusCode();
        } catch (Exception | RequestException | GuzzleException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
