<?
    if ( !defined("_NB_VALID_") ) exit;

    define("SEC_HEADER_ENABLED", false);
    define("SEC_HEADER_CONFIG", [
        "hsts" => "max-age=63072000; includeSubDomains",
        "common" => [
            "X-Content-Type-Options" => "nosniff"
        ],
        "general" => [
            "Content-Security-Policy" => [
                "enabled"     => true,
                "nonce"       => false,
                "default-src" => [ "self" ]
            ],
            "X-Frame-Options" => "SAMEORIGIN",
        ],
        "api" => [
            "Access-Control-Allow-Origin" => [],
            "Access-Control-Allow-Credentials" => true,
            "Access-Control-Allow-Headers" => []
        ]
    ]);