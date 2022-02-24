<?php

// SSLCommerz configuration

return [
    'is_sandbox' => env('IS_SSL_SANDBOX', true),
    'sandbox' => [
        'projectPath' => env('PROJECT_PATH', ''),
        'apiDomain' => "https://sandbox.sslcommerz.com",
        'apiCredentials' => [
            'store_id' => 'nasciborgbdlive',
            'store_password' => '612234D7EAA9D58156',
        ],
        'apiUrl' => [
            'make_payment' => "/gwprocess/v4/api.php",
            'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
            'order_validate' => "/validator/api/validationserverAPI.php",
            'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
            'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        ],
        'connect_from_localhost' => true, // For Sandbox, use "true", For Live, use "false"
        'success_url' => '/success',
        'failed_url' => '/fail',
        'cancel_url' => '/cancel',
        'ipn_url' => '/ipn',
    ],
    "production" => [
        'projectPath' => env('PROJECT_PATH', ''),
        'apiDomain' => "https://sandbox.sslcommerz.com",
        'apiCredentials' => [
            'store_id' => 'nasciborgbdlive',
            'store_password' => '612234D7EAA9D58156',
        ],
        'apiUrl' => [
            'make_payment' => "/gwprocess/v4/api.php",
            'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
            'order_validate' => "/validator/api/validationserverAPI.php",
            'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
            'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        ],
        'connect_from_localhost' => false, // For Sandbox, use "true", For Live, use "false"
        'success_url' => '/success',
        'failed_url' => '/fail',
        'cancel_url' => '/cancel',
        'ipn_url' => '/ipn',
    ]

];
