<?php

namespace App\Helpers\Classes;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpClientRequest
{

    public function getAuthPermission(?string $idp_user_id)
    {

        $url = BaseModel::ORGANIZATION_USER_REGISTRATION_ENDPOINT_LOCAL . 'auth-user-info';
        if (!in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            $url = BaseModel::ORGANIZATION_USER_REGISTRATION_ENDPOINT_REMOTE . 'auth-user-info';
        }

        $userPostField = [
            "idp_user_id" => $idp_user_id
        ];

        $responsetData = Http::retry(3)->post($url, $userPostField)->throw(function ($response, $e) {
            return $e;
        })->json('data');

        Log::info("userInfo:" . json_encode($responsetData));

        return $responsetData;
    }

}
