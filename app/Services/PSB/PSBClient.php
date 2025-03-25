<?php

namespace App\Services\PSB;

use App\Models\TransactionLog\TransactionLog;
use App\Services\PSB\Dto\CreatePaymentDto;
use App\Services\PSB\Dto\PaymentDto;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use RuntimeException;

/**
 * Class SberbankClient
 *
 * @package App\Services\Sberbank
 */
class PSBClient
{
    private HttpClient $httpClient;

    public function __construct(
        string $baseUri,
        private array $data
    ) {
        $this->httpClient = new HttpClient([
            'base_uri' => $baseUri,
        ]);
    }

    public function createPayment(array $data)
    {
        $params = array_change_key_case($data, CASE_UPPER);
        $query = http_build_query($params);

        return $this->request(
            config('psb.base_uri'),
            config('psb.host'),
            $query,
            $data
        );
    }

    private function request(string $url_data, string $host_data, $query, array $data)
    {
        $url = $url_data;
        $host = $host_data;
        $headers = ["Host: " . $host,"User-Agent: " . $_SERVER['HTTP_USER_AGENT']
            ,"Accept: */*","Content-Type: application/x-www-form-urlencoded; charset=utf-8"];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if (!$response) {
            return curl_error($ch);
        }

        curl_close($ch);
        return json_decode($response)->REF;
    }
}
