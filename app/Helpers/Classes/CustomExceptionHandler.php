<?php namespace App\Helpers\Classes;

use Exception;

class CustomExceptionHandler
{
    /**      *
     * @param $messageBody
     * @throws Exception
     */
    public static function customHttpResponseMessage($messageBody)
    {
        if (!empty($messageBody)) {
            $body = json_decode($messageBody, true);
            if (!empty($body['_response_status']) && !empty($body['_response_status']['code'])) {
                $code = $body['_response_status']['code'];
                $message = [];
                if (!empty($body['errors'])) {
                    $message["errors"] = $body['errors'];
                }
                if (!empty($body['_response_status']['message'])) {
                    $message["message"] = $body['_response_status']['message'];
                }
                throw new Exception(json_encode($message), $code);
            }
        }
        throw new Exception("Something went wrong in internal service to service calling!", 500);
    }
}
