<?php

namespace App\Services\Apple;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use RuntimeException;

/**
 * Class AppleClient
 *
 * @package App\Services\Apple
 */
class AppleClient
{
    private HttpClient $httpClient;

    public function validateRequest(string $validateUrl): array
    {
        $this->httpClient = new HttpClient([
            'base_uri' => $validateUrl,
        ]);

        $options = [
            RequestOptions::FORM_PARAMS => [
                'merchantIdentifier' => '',
                'domainName' => '',
                'displayName' => '',
            ],
            'curl' => [
                CURLOPT_CERTINFO => true,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_SSLCERT => '',
                CURLOPT_SSLKEY => '',
                CURLOPT_SSLKEYPASSWD => '',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ],
        ];

        try {
            $response = $this->httpClient->request('POST', '', $options);

            return json_decode($response->getBody(), true);
        } catch (Exception | RequestException | GuzzleException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
