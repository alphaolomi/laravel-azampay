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
];
