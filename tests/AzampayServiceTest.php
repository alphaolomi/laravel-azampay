<?php

use Alphaolomi\Azampay\AzampayService;
use Alphaolomi\Azampay\Events\AzampayCallback;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use function Pest\Laravel\post;

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

it('can get payment partners successful', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/get_partners_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/api/v1/Partner/GetPaymentPartners' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/api/v1/Partner/GetPaymentPartners' => Http::response($stub, 200),
    ]);

    $azampay = new AzampayService();

    $data = $azampay->getPaymentPartners();

    $this->assertEquals($data, $stub);
});

it('can throw exception if get partners url respond with error', function () {
    Http::fake([
        AzampayService::BASE_URL.'/api/v1/Partner/GetPaymentPartners' => Http::response('error string', 400),
        AzampayService::SANDBOX_BASE_URL.'/api/v1/Partner/GetPaymentPartners' => Http::response('error string', 400),
    ]);

    $azampay = new AzampayService();

    $data = $azampay->getPaymentPartners();

    $this->assertEquals($data, 'error string');
})->throws(RuntimeException::class, 'error string');

it('can post checkout successful', function () {
    Http::fake([
        AzampayService::BASE_URL.'/api/v1/Partner/PostCheckout' => Http::response('string', 200),
        AzampayService::SANDBOX_BASE_URL.'/api/v1/Partner/PostCheckout' => Http::response('string', 200),
    ]);

    $azampay = new AzampayService();

    $data = $azampay->postCheckout([
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

it('can create transfer successful', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/createtransfer_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/createtransfer' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/createtransfer' => Http::response($stub, 200),
    ]);

    $azampay = new AzampayService();

    $data = $azampay->createTransfer([
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

it('can name lookup successful', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/namelookup_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/namelookup' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/namelookup' => Http::response($stub, 200),
    ]);

    $azampay = new AzampayService();

    $data = $azampay->nameLookup([
        'bankName' => 'string',
        'accountNumber' => 'string',
    ]);

    $this->assertEquals($data, $stub);
});

it('can get transaction status successful', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/transactionstatus_success.json'),
        true
    );

    Http::fake([
        AzampayService::BASE_URL.'/azampay/gettransactionstatus?*' => Http::response($stub, 200),
        AzampayService::SANDBOX_BASE_URL.'/azampay/gettransactionstatus?*' => Http::response($stub, 200),
    ]);

    $azampay = new AzampayService();

    $data = $azampay->getTransactionStatus(['bankName' => 'CRDB', 'pgReferenceId' => 'omakei']);

    $this->assertEquals($data, $stub);
});

it('can receive callback and dispatch azampay callback event', function () {
    $stub = json_decode(
        file_get_contents(__DIR__.'/stubs/responses/callback.json'),
        true
    );
    Event::fake([
        AzampayCallback::class,
    ]);

    post(route('checkout_payment_callback'), $stub)->assertStatus(200);

    Event::assertDispatched(AzampayCallback::class);
});
