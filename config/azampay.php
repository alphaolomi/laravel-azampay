<?php

/**
 * Config for Azampay
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Azampay Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application as provided on Azampay.
    |
    */
    'appName' => env('AZAMPAY_APP_NAME', 'Azampay'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Client ID
    |--------------------------------------------------------------------------
    |
    | This value is the client ID of your application as provided on Azampay.
    |
    */
    'clientId' => env('AZAMPAY_CLIENT_ID', 'Azampay'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Client Secret
    |--------------------------------------------------------------------------
    |
    | This value is the client secret of your application as provided on Azampay.
    |
    */
    'clientSecret' => env('AZAMPAY_CLIENT_SECRET', 'Azampay'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Environment
    |--------------------------------------------------------------------------
    |
    | This value is the environment of your application as registered on Azampay.
    | Available options are: sandbox, production/live
    */
    'environment' => env('AZAMPAY_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Token
    |--------------------------------------------------------------------------
    |
    | This value is the token of your application as registered on Azampay.
    | It is secure API token for your application to receive callback of
    | the payment from Azampay Payment API. You need to validate
    | this while receiving callback from Azampay Payment API.
    */
    'token' => env('AZAMPAY_TOKEN', 'Azampay'),

    /*
    |--------------------------------------------------------------------------
    | Base URLs
    |--------------------------------------------------------------------------
    |
    | Leave these empty to use AzamPay's hosts for the environment above.
    | Set one to point the package somewhere else, for example at a local
    | simulator in development or tests.
    */
    'urls' => [
        'auth' => env('AZAMPAY_AUTH_URL'),
        'checkout' => env('AZAMPAY_CHECKOUT_URL'),
        'disbursement' => env('AZAMPAY_DISBURSEMENT_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Access token cache
    |--------------------------------------------------------------------------
    |
    | Seconds to keep the access token in your application's cache, so a new
    | token is not requested on every request. It is never kept past the
    | expiry AzamPay reports. Set to 0 to request a token every time.
    */
    'token_cache_ttl' => (int) env('AZAMPAY_TOKEN_CACHE_TTL', 3300),

    /*
    |--------------------------------------------------------------------------
    | Request timeout
    |--------------------------------------------------------------------------
    |
    | Seconds to wait for the disbursement API before giving up.
    */
    'timeout' => (int) env('AZAMPAY_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Disbursement
    |--------------------------------------------------------------------------
    |
    | The account transfers are paid from, and the public key AzamPay issued
    | to you for the transfer checksum. The key can be the PEM contents (use
    | "\n" for line breaks in .env) or a path to a PEM file.
    */
    'disbursement' => [
        'public_key' => env('AZAMPAY_DISBURSEMENT_PUBLIC_KEY'),

        // Endpoint paths, relative to the disbursement base URL.
        'paths' => [
            'name_lookup' => env('AZAMPAY_NAME_LOOKUP_PATH', 'api/v1/azampay/namelookup'),
            'disburse' => env('AZAMPAY_DISBURSE_PATH', 'api/v1/azampay/disburse'),
            'transaction_status' => env('AZAMPAY_TRANSACTION_STATUS_PATH', 'api/v1/azampay/transactionstatus'),
        ],

        'source' => [
            'accountNumber' => env('AZAMPAY_SOURCE_ACCOUNT'),
            'fullName' => env('AZAMPAY_SOURCE_NAME'),
            'bankName' => env('AZAMPAY_SOURCE_BANK', 'Azampesa'),
            'countryCode' => env('AZAMPAY_SOURCE_COUNTRY', 'TZ'),
            'currency' => env('AZAMPAY_SOURCE_CURRENCY', 'TZS'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Callbacks
    |--------------------------------------------------------------------------
    |
    | The routes AzamPay calls with the result of a checkout or a transfer.
    |
    | Turn `verify` on to reject checkout callbacks whose signature does not
    | match AzamPay's public key. The key is fetched from AzamPay and cached;
    | set `public_key` (PEM contents or a file path) to use a fixed key.
    | `middleware` is added to both routes, for example a rate limiter.
    */
    'callback' => [
        'enabled' => (bool) env('AZAMPAY_CALLBACK_ENABLED', true),
        'path' => env('AZAMPAY_CALLBACK_PATH', '/api/v1/Checkout/Callback'),
        'middleware' => [],

        'verify' => (bool) env('AZAMPAY_VERIFY_CALLBACKS', false),
        'public_key' => env('AZAMPAY_CALLBACK_PUBLIC_KEY'),
        'public_key_path' => env('AZAMPAY_CALLBACK_PUBLIC_KEY_PATH', 'azampay/v1/public-key'),
        'public_key_cache_ttl' => (int) env('AZAMPAY_CALLBACK_PUBLIC_KEY_TTL', 86400),
        'signature_fields' => ['utilityref', 'externalreference', 'transactionstatus', 'operator'],

        'disbursement_enabled' => (bool) env('AZAMPAY_DISBURSEMENT_CALLBACK_ENABLED', false),
        'disbursement_path' => env('AZAMPAY_DISBURSEMENT_CALLBACK_PATH', '/api/v1/Disbursement/Callback'),
    ],
];
