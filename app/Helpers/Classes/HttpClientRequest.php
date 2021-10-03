<?php

namespace App\Helpers\Classes;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Object_;

class HttpClientRequest
{

    public function getAuthPermission(?string $idp_user_id)
    {

        $url = clientUrl(BaseModel::CORE_CLIENT_URL_TYPE) . 'auth-user-info';

        $userPostField = [
            "idp_user_id" => $idp_user_id
        ];

        $responseData = Http::retry(3)->post($url, $userPostField)->throw(function ($response, $e) {
            return $e;
        })->json('data');

        Log::info("userInfo:" . json_encode($responseData));

        return $responseData;
    }

}
