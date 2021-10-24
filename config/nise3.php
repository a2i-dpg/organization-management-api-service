<?php

return [
    "is_dev_mode" => env("IS_DEVELOPMENT_MODE", false),
    'http_debug' => env("HTTP_DEBUG_MODE", false),
    "should_ssl_verify" => env("IS_SSL_VERIFY", false),
    "http_timeout" => env("HTTP_TIMEOUT", 60),
];
