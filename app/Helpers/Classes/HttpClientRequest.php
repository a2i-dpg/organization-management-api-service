<?php

namespace App\Helpers\Classes;

use App\Models\BaseModel;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpClientRequest
{

    /**
     * @throws RequestException
     */
    public function getAuthPermission(?string $idp_user_id)
    {

        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'auth-user-info';
        $userPostField = [
            "idp_user_id" => $idp_user_id
        ];

        $responseData = Http::retry(3)
            ->withOptions(['debug' => config("nise3.is_dev_mode"), 'verify' => config("nise3.should_ssl_verify")])
//            ->withOptions(['debug' => env("IS_DEVELOPMENT_MOOD", false), 'verify' => env("IS_SSL_VERIFY", false)])
            ->post($url, $userPostField)
            ->throw(function ($response, $e) {
                return $e;
            })
            ->json('data');

        Log::info("userInfo:" . json_encode($responseData));

        return $responseData;
    }

}
