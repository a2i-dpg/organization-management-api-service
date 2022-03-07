<?php

namespace App\Services\PaymentService;


use App\Services\PaymentService\Library\SslCommerzNotification;

class SSLCommerzPaymentService
{
    public function makePayment(array $config, array $payload)
    {
        $sslPayment = new SslCommerzNotification($config);
        return $sslPayment->makePayment($payload);

    }
}
