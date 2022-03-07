<?php

namespace App\Services\PaymentService\Library;


use App\Exceptions\HttpErrorException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractSslCommerz implements SslCommerzInterface
{
    protected $apiUrl;
    protected $storeId;
    protected $storePassword;

    protected function setStoreId($storeID)
    {
        $this->storeId = $storeID;
    }

    protected function getStoreId()
    {
        return $this->storeId;
    }

    protected function setStorePassword($storePassword)
    {
        $this->storePassword = $storePassword;
    }

    protected function getStorePassword()
    {
        return $this->storePassword;
    }

    protected function setApiUrl($url)
    {
        $this->apiUrl = $url;
    }

    protected function getApiUrl()
    {
        return $this->apiUrl;
    }


    public function callToApi($data, $header = [], $setLocalhost = false)
    {
        Log::channel('ssl_commerz')->info("Ssl-callToApi:" . json_encode([
                "post_payload" => $data,
                "header" => $header,
                "setLocalhost" => $setLocalhost
            ]));

        $curl = curl_init();

        if (!$setLocalhost) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // The default value for this option is 2. It means, it has to have the same name in the certificate as is in the URL you operate against.
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // When the verify value is 0, the connection succeeds regardless of the names in the certificate.
        }

        curl_setopt($curl, CURLOPT_URL, $this->getApiUrl());
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlErrorNo = curl_errno($curl);
        curl_close($curl);

        Log::info("curl_getinfo:" . json_encode([
                "response" => $response,
                "err" => $err,
                "curl_getinfo" => $code,
                "curl_errno" => $curlErrorNo
            ]));

        if ($code == 200 & !($curlErrorNo)) {
            return $response;
        } else {
            return "FAILED TO CONNECT WITH SSLCOMMERZ API";
        }
    }

    /**
     * @param array $sslcz
     * @param string $type
     * @param string $pattern
     * @return array
     */
    public function formatResponse(array $sslcz, $type = 'checkout', $pattern = 'json'): array
    {

        if ($type != 'checkout') {
            return $sslcz;
        } else {
            if ($sslcz['status'] == 'SUCCESS') {
                $response = [
                    'gateway_page_url' => $sslcz['GatewayPageURL'],
                    'status' => 'success',
                    "message" => "Ssl Payment is successfully initialized"
                ];
            } else {
                $response = [
                    'status' => 'fail',
                    'message' => $sslcz['failedreason']
                ];
            }
        }
        return $response;
    }

    /**
     * @param $url
     * @param bool $permanent
     */
    public function redirect($url, $permanent = false)
    {
        header('Location: ' . $url, true, $permanent ? 301 : 302);

        exit();
    }
}
