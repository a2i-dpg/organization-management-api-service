<?php

use App\Models\IndustryAssociationConfig;


return [
    "is_dev_mode" => env("IS_DEVELOPMENT_MODE", false),
    'http_debug' => env("HTTP_DEBUG_MODE", false),
    "should_ssl_verify" => env("IS_SSL_VERIFY", false),
    "http_timeout" => env("HTTP_TIMEOUT", 60),
    "user_cache_ttl" => 86400,
    'payment_config' => [
        'session_type_wise_expiration_date' => [
            IndustryAssociationConfig::SESSION_TYPE_JUNE_JULY => [
                'start_date' => date('Y', strtotime('-1 year')) . '-07-01',
                'end_date' => date('Y') . '-06-31',
            ],
            IndustryAssociationConfig::SESSION_TYPE_JANUARY_DECEMBER => [
                'start_date' => date('Y') . '-01-01',
                'end_date' => date('Y') . '-12-31',
            ],
        ]
    ]
];
