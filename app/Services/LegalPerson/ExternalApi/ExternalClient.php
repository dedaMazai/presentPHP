<?php

namespace App\Services\LegalPerson\ExternalApi;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use RuntimeException;

/**
 * Class ExternalClient
 *
 * @package App\Services\LegalPerson\ExternalApi
 */
class ExternalClient
{
    public function __construct(
        private HttpClient $httpClient
    ) {
    }

    public function getAccountFill(string $inn): array
    {
        return $this->request(
            'POST',
            ExternalApiRequest::SUGGESTION_DADATA_POST,
            [
                RequestOptions::JSON => [
                    'query' => $inn,
                ],
                RequestOptions::HEADERS => [
                    'Authorization' => 'Token 5fc3db35b00c4c2f712fa626b5891911352075c0',
                ],
            ],
        );
    }

    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $uri, $options);

            return json_decode($response->getBody(), true);
        } catch (Exception | RequestException | GuzzleException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
