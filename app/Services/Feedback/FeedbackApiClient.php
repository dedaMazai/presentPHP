<?php

namespace App\Services\Feedback;

use App\Models\User\User;
use App\Services\DynamicsCrm\Exceptions\BadRequestException;
use App\Services\DynamicsCrm\Exceptions\NotFoundException;
use App\Services\Feedback\Dto\SaveFeedbackDto;
use App\Services\SettingsService;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RuntimeException;

/**
 * Class FeedbackApiClient
 *
 * @package App\Services\DynamicsCrm
 */
class FeedbackApiClient
{
    private HttpClient $httpClient;
    private string $baseUri;
    public function __construct(string $baseUri)
    {
        $client = new HttpClient([
            'base_uri' => $baseUri,
        ]);

        $this->baseUri = $baseUri;

        $this->httpClient = $client;
    }


    /**
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function createFeedback(SaveFeedbackDto $dto): array
    {
        $appVersion = $dto->app_version??'';
        $osVersion = $dto->os_version??'';

        $description = "<strong>Сообщение клиента:</strong><br>$dto->message<br>".
            "<strong>Версия приложения:</strong><br>$appVersion<br>".
            "<strong>Версия операционной системы:</strong><br>$osVersion<br>";

        // phpcs:disable
        return $this->request(
            'POST',
            $this->baseUri . '/v2/tickets',
            [
                RequestOptions::FORM_PARAMS  => [
                    'title' => $dto->message,
                    'user_email' => $dto->email,
                    'department_id' => '5',
                    'custom_fields[1]' => '121',
                    'custom_fields[4]' => $dto->name,
                    'custom_fields[5]' => $dto->phone,
                    'description' => $description,
                ],
                RequestOptions::HEADERS => [
                    'Authorization' => 'Basic ZW1wLWFwaUBwaW9uZWVyLnJ1OmY3ZDY1YWZiLTlmMzktNDNhMC05MDdlLTVjMzg1MjY0NGY5Nw==',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]
        );
        // phpcs:enable
    }

    /**
     * @throws BadRequestException
     * @throws NotFoundException
     */
    private function request(string $method, string $uri, array $options = [], int $timeout = 60)
    {
        try {
            //TODO: remove when CRM will be stable
            $options[RequestOptions::VERIFY] = false;
            $options[RequestOptions::TIMEOUT] = $timeout;

            $start = microtime(true);
            $response = $this->httpClient->request($method, $uri, $options);
            $time_elapsed_secs = microtime(true) - $start;

            if (isset($_SERVER)) {
                $request_url = $_SERVER['REQUEST_URI'] ?? null;
            } else {
                $request_url = '';
            }

            $str_options = json_encode($options);
            $str_response = json_encode(json_decode($response->getBody(), true));

            $text = date('Y-m-d H:i:s').' | request: '.
                $request_url. " | CRM request: $uri | " . "options: $str_options | " .
                "response: $str_response | " . round($time_elapsed_secs, 2). " s";

            $this->getLogger()->info($text);

            if ($response->getStatusCode() === 201) {
                return null;
            }

            return json_decode($response->getBody(), true);
        } catch (Exception | RequestException | GuzzleException $e) {
            if ($e->getCode() === 404) {
                throw new NotFoundException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
            } elseif ($e->getCode() === 400) {
                throw new BadRequestException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
            }

            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function getLogger()
    {
        $dateString = now()->format('d_m_Y');
        $filePath = 'feedback_' . $dateString . '.log';
        $dateFormat = "m/d/Y H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $stream = new StreamHandler(storage_path('logs/' . $filePath), Logger::DEBUG);
        umask(0002);
        $stream->setFormatter($formatter);
        $processId = Str::random(5);
        $logger = new Logger($processId);
        $logger->pushHandler($stream);

        return $logger;
    }
}
