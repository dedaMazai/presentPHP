<?php

namespace App\Services\Crm;

use App\Models\Meter\MeterSubtype;
use App\Models\Meter\MeterType;
use App\Models\User\User;
use App\Services\Account\Dto\RefillBalanceDto;
use App\Services\Claim\Dto\SetClaimPaidDto;
use App\Services\Meter\Dto\SaveMeterValueDto;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RuntimeException;

/**
 * Class CrmClient
 *
 * @package App\Services\Crm
 */
class CrmClient
{
    private HttpClient $httpClient;

    public function __construct(string $apiKey, string $baseUri)
    {
        $client = new HttpClient([
            'base_uri' => $baseUri,
            'headers' => [
                'X-API-KEY' => $apiKey,
            ],
        ]);

        $this->httpClient = $client;
    }

    /**
     * @throws ValidationException
     */
    public function getAccounts(User $user): array
    {
        return $this->request(
            'GET',
            'accounts_list/' . $user->crm_id,
        );
    }

    /**
     * @throws ValidationException
     */
    public function getAccountByNumber(string $accountNumber): array
    {
        try {
            $account = $this->request(
                'POST',
                'accounts',
                [
                    RequestOptions::JSON => [
                        $accountNumber,
                    ],
                ],
            );
        } catch (\Throwable) {
            $account = [];
        }

        return $account;
    }

    public function getAccountDocumentsByAccountNumber(string $accountNumber): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Акт приёма передачи',
                'url' => 'https://yandex.ru',
            ],
            [
                'id' => 2,
                'name' => 'Договор управления с УК',
                'url' => 'https://yandex.ru',
            ],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function getReceipts(
        string $accountNumber,
        Carbon $startDate,
        Carbon $endDate,
    ): array {
        return $this->request(
            'GET',
            'accounts/' . $accountNumber . '/receipts',
            [
                RequestOptions::QUERY => [
                    'start_date' => $startDate->format('d.m.Y H:i:s'),
                    'end_date' => $endDate->format('d.m.Y H:i:s'),
                ],
            ],
        );
    }

    /**
     * @throws ValidationException
     */
    public function getMeters(
        string $accountNumber,
        ?MeterType $type = null,
        ?MeterSubtype $subtype = null,
    ): array {
        $query = [];
        if ($type) {
            $query['type'] = $type->value;

            if ($subtype) {
                $query['subtype'] = $subtype->value;
            }
        }

        return $this->request(
            'GET',
            'accounts/' . $accountNumber . '/meters',
            [
                RequestOptions::QUERY => $query,
            ],
        );
    }

    /**
     * @throws ValidationException
     */
    public function getMeterTariffs(string $accountNumber): array
    {
        return $this->request(
            'GET',
            'accounts/' . $accountNumber . '/meters/tariffs',
        );
    }

    /**
     * @throws ValidationException
     */
    public function getMeterStatistics(
        string $accountNumber,
        MeterType $type,
        ?MeterSubtype $subtype,
        Carbon $startDate,
        Carbon $endDate,
    ): array {
        $query = [
            'type' => $type->value,
            'start_date' => $startDate->format('d.m.Y H:i:s'),
            'end_date' => $endDate->format('d.m.Y H:i:s'),
        ];
        if ($subtype) {
            $query['subtype'] = $subtype->value;
        }

        return $this->request(
            'GET',
            'accounts/' . $accountNumber . '/meters/statistics',
            [
                RequestOptions::QUERY => $query,
            ],
        );
    }

    /**
     * @throws ValidationException
     */
    public function getMeterStatisticsType(
        string $accountNumber,
        string $startDate,
        string $endDate,
    ): array {
        $query = [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        return $this->request(
            'GET',
            'accounts/' . $accountNumber . '/meters/statistics',
            [
                RequestOptions::QUERY => $query,
            ],
        );
    }

    /**
     * @throws ValidationException
     */
    public function getMeterEnterPeriod(string $buildingAddressId): array
    {
        return $this->request(
            'GET',
            'accounts/' . $buildingAddressId . '/meters/enter-period',
        );
    }

    /**
     * @throws ValidationException
     */
    public function checkMeterStatistics(string $accountNumber): array
    {
        return $this->request(
            'GET',
            'accounts/' . $accountNumber . '/meters-statistics-check',
        );
    }

    /**
     * @throws ValidationException
     */
    public function saveMeterValue(string $accountNumber, SaveMeterValueDto $dto): array
    {
        $data = [
            'meter_id' => $dto->id,
        ];
        foreach ($dto->values as $itemDto) {
            $data[$itemDto->tariffId] = $itemDto->currentValue;
        }

        return $this->request(
            'POST',
            'accounts/' . $accountNumber . '/meters/save',
            [
                RequestOptions::JSON => [
                    $data,
                ],
            ],
        );
    }

    /**
     * @throws ValidationException
     */
    public function refillBalance(RefillBalanceDto $dto, string $paymentMethod): void
    {
        $this->request(
            'POST',
            'accounts/' . $dto->accountNumber . '/pay',
            [
                RequestOptions::JSON => [
                    'user_crm_id' => $dto->user->crm_id,
                    'email' => $dto->user->email,
                    'payment_sum' => $dto->amount,
                    'payment_acquirer_id' => $dto->paymentId,
                    'payment_date' => $dto->paymentDateTime->toIso8601String(),
                    'payment_method' => $paymentMethod
                ],
            ],
        );
    }

    /**
     * @throws ValidationException
     */
    public function setClaimPaid(User $user, SetClaimPaidDto $dto, string $paymentMethod): array
    {
        return $this->request(
            'POST',
            'claims/' . $dto->claim->getId() . '/pay',
            [
                RequestOptions::JSON => [
                    'payment_acquirer_id' => $dto->paymentId,
                    'payment_date' => $dto->paymentDateTime->toAtomString(),
                    'client_crm_id' => $user->crm_id,
                    'email' => $user->email,
                    'payment_method' => $paymentMethod,
                ],
            ],
        );
    }

    /**
     * @throws ValidationException
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            //TODO: remove when CRM will be stable
            $options[RequestOptions::VERIFY] = false;
            $options[RequestOptions::TIMEOUT] = 120;

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

            return json_decode($response->getBody(), true);
        } catch (Exception | RequestException | GuzzleException $e) {
            if ($e->getCode() == 422) {
                $messages = [];
                $errors = json_decode($e->getResponse()->getBody(), true);
                foreach ($errors as $error) {
                    $messages[$error['field']] = $error['description'];
                }

                throw ValidationException::withMessages($messages);
            } elseif ($e->getCode() == 409) {
                $error = json_decode($e->getResponse()->getBody(), true);

                throw new RuntimeException($error['message'], $e->getCode(), $e);
            }

            throw new RuntimeException(
                mb_convert_encoding($e->getResponse()->getBody()->getContents(), 'UTF-8', 'UTF-8'),
                $e->getCode(),
                $e,
            );
        }
    }

    private function getLogger()
    {
        $dateString = now()->format('d_m_Y');
        $filePath = 'crm_1c_requests_' . $dateString . '.log';
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
