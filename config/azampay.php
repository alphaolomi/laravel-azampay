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
    | This value is the name of your application as registered on Azampay.
    |
    */
    'appName' => env('AZAMPAY_APP_NAME', 'Azampay'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Client ID
    |--------------------------------------------------------------------------
    |
    | This value is the client ID of your application as registered on Azampay.
    |
    */
    'clientId' => env('AZAMPAY_CLIENT_ID', 'Azampay'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Client Secret
    |--------------------------------------------------------------------------
    |
    | This value is the client secret of your application as registered on Azampay.
    |
    */
    'clientSecret' => env('AZAMPAY_CLIENT_SECRET', 'Azampay'),

    /*
    |--------------------------------------------------------------------------
    | Azampay Environment
    |--------------------------------------------------------------------------
    |
    | This value is the environment of your application as registered on Azampay.
    |
    */
    'environment' => env('AZAMPAY_ENVIRONMENT', 'sandbox'),
];
