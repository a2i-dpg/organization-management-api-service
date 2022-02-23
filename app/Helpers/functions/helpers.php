<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

if (!function_exists("clientUrl")) {
    function clientUrl($type)
    {
        return config("httpclientendpoint." . $type);
    }
}

if (!function_exists('formatApiResponse')) {
    /**
     * @param $data
     * @param $startTime
     * @param int $statusCode
     * @return array
     */
    function formatApiResponse($data, $startTime, int $statusCode = 200): array
    {
        return [
            "data" => $data ?: null,
            "_response_status" => [
                "success" => true,
                "code" => $startTime,
                "query_time" => $startTime->diffForHumans(Carbon::now())
            ]
        ];
    }
}

if (!function_exists("idpUserErrorMessage")) {

    /**
     * @param $exception
     * @return array
     */
    function idUserErrorMessage($exception): array
    {
        $statusCode = $exception->getCode();
        Log::info("status.code" . $statusCode);
        $errors = [
            '_response_status' => [
                'success' => false,
                'code' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Idp User unknown error",
                "query_time" => 0
            ]
        ];

        switch ($statusCode) {
            case ResponseAlias::HTTP_UNPROCESSABLE_ENTITY:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY;
                $errors['_response_status']['message'] = "Username already exists in IDP";
                return $errors;
            }
            case ResponseAlias::HTTP_NOT_FOUND:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_NOT_FOUND;
                $errors['_response_status']['message'] = "IDP user not found";
                return $errors;
            }
            case ResponseAlias::HTTP_UNAUTHORIZED:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_UNAUTHORIZED;
                $errors['_response_status']['message'] = "HTTP 401 Unauthorized Error in IDP server";
                return $errors;
            }
            case ResponseAlias::HTTP_BAD_REQUEST:
            {
                $errors['_response_status']['code'] = ResponseAlias::HTTP_BAD_REQUEST;
                $errors['_response_status']['message'] = "HTTP 400 BAD Request Error in IDP server";
                return $errors;
            }
            case 0:
            {
                $errors['_response_status']['message'] = $exception->getHandlerContext()['error'] ?? " SSL Certificate Error: An expansion of the 400 Bad Request response code, used when the client has provided an invalid client certificate";
                return $errors;
            }
            default:
            {
                return $errors;
            }

        }
    }
}

if (!function_exists("getLocationIdByKeyString")) {
    /**
     * @param string $keyString
     * @return array
     */
    function getLocationIdByKeyString(string $keyString): array
    {
        return explode("_", $keyString);
    }
}

if (!function_exists("bearerUserToken")) {

    function bearerUserToken(\Illuminate\Http\Request $request, $headerName = 'User-Token')
    {
        $header = $request->header($headerName);

        $position = strrpos($header, 'Bearer ');

        if ($position !== false) {
            $header = substr($header, $position + 7);
            return strpos($header, ',') !== false ? strstr(',', $header, true) : $header;
        }
    }
}

if (!function_exists("logSelector")) {

    /**
     * @return array
     */
    function logSelector(): array
    {
        if (env('LOG_CHANNEL') == 'elasticsearch') {
            return config('elasticSearchLogConfig');
        }
        return config('lumenDefaultLogConfig');
    }
}
