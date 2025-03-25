<?php

namespace App\Services\SBP;

use App\Models\TransactionLog\TransactionLog;
use GuzzleHttp\Client as HttpClient;

/**
 * Class SberbankClient
 *
 * @package App\Services\Sberbank
 */
class SBPClient
{
    public function createPayment(array $data)
    {
        $params = array_change_key_case($data, CASE_UPPER);
        $query = http_build_query($params);

        return $this->request(
            config('sbp.base_uri'),
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
        return ['url' => json_decode($response)->QR_DATA, 'qr_id' => json_decode($response)->QR_ID];
    }
}
