<?php


use Alphaolomi\Azampay\AzampayService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $auth_stub = json_decode(
        file_get_contents(__DIR__ . '/stubs/responses/generate_token_success.json'),
        true
    );

    Http::fake([
        AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response($auth_stub, 200),
        AzampayService::AUTH_BASE_URL.'/*' => Http::response($auth_stub, 200),
    ]);

});

it(/**
 * @throws Exception
 */ 'can successful and send mobile checkout request', function(){
    $stub = json_decode(
        file_get_contents(__DIR__ . '/stubs/responses/bank_checkout_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/mno/checkout' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/mno/checkout' => Http::response($stub, 200),
    ]);

    $azampay = new AzampayService();

    $data = $azampay->mobileCheckout([
        'amount' => 1000,
        'currency' => 'TZS',
        'accountNumber' => '0625933171',
        'externalId' => '08012345678',
        'provider' => 'Mpesa',
    ]);

    $this->assertEquals($data, $stub);
});


it('can throw exception if mobile checkout request return error', function(){
    $stub = json_decode(
        file_get_contents(__DIR__ . '/stubs/responses/bank_checkout_failure.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/mno/checkout' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/mno/checkout' => Http::response($stub, 200),
    ]);

    $azampay = new AzampayService();

    $data = $azampay->mobileCheckout([
        'amount' => 1000,
        'currency' => 'TZS',
        'accountNumber' => '0625933171',
        'externalId' => '08012345678',
        'provider' => 'Mpesa',
    ]);

    $this->assertEquals($data, $stub);
});

