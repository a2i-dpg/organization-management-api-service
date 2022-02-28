<?php

// SSLCommerz configuration

return [
    'sandbox' => [
        'apiDomain' => 'https://sandbox.sslcommerz.com',
        'apiUrl' => [
            'make_payment' => '/gwprocess/v4/api.php',
            'refund_status' => '/validator/api/merchantTransIDvalidationAPI.php',
            'order_validate' => '/validator/api/validationserverAPI.php',
            'refund_payment' => '/validator/api/merchantTransIDvalidationAPI.php',
            'transaction_status' => '/validator/api/merchantTransIDvalidationAPI.php',
        ],
        'projectPath' => '',
        'apiCredentials' => [
            'store_id' => 'nise6213e7cbf22a4',
            'store_password' => 'nise6213e7cbf22a4@ssl',
        ],
        "ipn_url" => "/api/v1/payment/nascib-member-ship-pay-via-ssl/ipn",
        'connect_from_localhost' => true,
    ],
    'production' => [
        'apiDomain' => 'https://merchant.sslcommerz.com',
        'apiUrl' => [
            'make_payment' => '/gwprocess/v4/api.php',
            'refund_status' => '/validator/api/merchantTransIDvalidationAPI.php',
            'order_validate' => '/validator/api/validationserverAPI.php',
            'refund_payment' => '/validator/api/merchantTransIDvalidationAPI.php',
            'transaction_status' => '/validator/api/merchantTransIDvalidationAPI.php',
        ],
        'projectPath' => '',
        'apiCredentials' => [
            'store_id' => 'nasciborgbdlive',
            'store_password' => '612234D7EAA9D58156',
        ],
        "ipn_url" => "/api/v1/payment/nascib-member-ship-pay-via-ssl/ipn",
        'connect_from_localhost' => false,
    ]

];
