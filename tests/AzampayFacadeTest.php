<?php

use Alphaolomi\Azampay\AzampayService;
use Alphaolomi\Azampay\Facades\Azampay;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $auth_stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/generate_token_success.json'),
        true
    );

    Http::fake([
        AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response($auth_stub, 200),
        AzampayService::AUTH_BASE_URL.'/*' => Http::response($auth_stub, 200),
    ]);
});

uses()->group('Azampay facade');

test('Bank Checkout', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/bank_checkout_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/bank/checkout' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/bank/checkout' => Http::response($stub, 200),
    ]);

    $data = Azampay::bankCheckout([
        'amount' => 1000,
        'currencyCode' => 'TZS',
        'merchantAccountNumber' => '34567890987654',
        'merchantMobileNumber' => '08012345678',
        'merchantName' => 'alphaolomi@gmail.com',
        'otp' => '1234',
        'provider' => 'CRDB',
        'referenceId' => '24345345',
    ]);

    $this->assertEquals($data, $stub);
});

test('MNO Checkout', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/bank_checkout_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/mno/checkout' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/mno/checkout' => Http::response($stub, 200),
    ]);

    $data = Azampay::mobileCheckout([
        'amount' => 1000,
        'currency' => 'TZS',
        'accountNumber' => '0625933171',
        'externalId' => '08012345678',
        'provider' => 'Mpesa',
    ]);

    $this->assertEquals($data, $stub);
});

test('get payment partners', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/get_partners_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/api/v1/Partner/GetPaymentPartners' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/api/v1/Partner/GetPaymentPartners' => Http::response($stub, 200),
    ]);

    $data = Azampay::getPaymentPartners();

    $this->assertEquals($data, $stub);
});

test('post checkout', function () {
    Http::fake([
        AzampayService::BASE_URL.'/api/v1/Partner/PostCheckout' => Http::response('string', 200),
        AzampayService::SANDBOX_BASE_URL.'/api/v1/Partner/PostCheckout' => Http::response('string', 200),
    ]);

    $data = Azampay::postCheckout([
        'appName' => 'azampay',
        'clientId' => 'e9b57fab-1850',
        'vendorId' => 'e9b57fab-1850-44d4-8499-71fd15c845a0',
        'language' => 'en',
        'currency' => 'TZS',
        'externalId' => 'e9b57fab-44d4-71fd15c845a6',
        'requestOrigin' => 'dukaspace.com',
        'redirectFailURL' => 'dukaspace.com/failure',
        'redirectSuccessURL' => 'dukaspace.com/success',
        'vendorName' => 'dukaspace',
        'amount' => 50000,
        'cart' => [
            'items' => [
                [
                    'name' => 'dukaspace',
                ],
            ],
        ],
    ]);

    $this->assertEquals($data, 'string');
});

test('create transfer', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/createtransfer_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/createtransfer' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/createtransfer' => Http::response($stub, 200),
    ]);

    $data = Azampay::createTransfer([
        'source' => [
            'countryCode' => 'string',
            'fullName' => 'string',
            'bankName' => 'tigo',
            'accountNumber' => 'string',
            'currency' => 'string',
        ],
        'destination' => [
            'countryCode' => 'string',
            'fullName' => 'string',
            'bankName' => 'tigo',
            'accountNumber' => 'string',
            'currency' => 'string',
        ],
        'transferDetails' => [
            'type' => 'string',
            'amount' => 0,
            'date' => '2019-08-24T141522Z',
        ],
        'externalReferenceId' => 'string',
        'remarks' => 'string',
    ]);

    $this->assertEquals($data, $stub);
});

test('name lookup', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/namelookup_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/namelookup' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/namelookup' => Http::response($stub, 200),
    ]);

    $data = Azampay::nameLookup([
        'bankName' => 'string',
        'accountNumber' => 'string',
    ]);

    $this->assertEquals($data, $stub);
});

test('get transaction status', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/transactionstatus_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/gettransactionstatus?*' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/gettransactionstatus?*' => Http::response($stub, 200),
    ]);

    $data = Azampay::getTransactionStatus(['bankName' => 'CRDB', 'pgReferenceId' => 'omakei']);

    $this->assertEquals($data, $stub);
});
