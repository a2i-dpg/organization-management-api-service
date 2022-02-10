<?php

namespace App\Services\CommonServices;

use App\Events\SmsSendEvent;

class SmsService
{

    public function sendSms(string $recipient, string $message)
    {
        $smsConfig = [
            "recipient" => $recipient,
            "message" => $message
        ];
        event(new SmsSendEvent($smsConfig));
    }

}
