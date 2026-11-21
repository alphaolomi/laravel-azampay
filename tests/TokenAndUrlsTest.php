<?php

use Alphaolomi\Azampay\AzampayService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

function azampayTokenResponse(string $token, ?string $expire = null): array
{
    return [
        'data' => ['accessToken' => $token, 'expire' => $expire ?? gmdate('Y-m-d\TH:i:s\Z', time() + 3600)],
        'message' => 'Token generated successfully',
        'success' => true,
        'statusCode' => 200,
    ];
}

function azampayTokenRequests(): int
{
    return Http::recorded(fn (Request $request) => str_contains($request->url(), '/AppRegistration/GenerateToken'))->count();
}

it('requests the access token once and reuses it from the cache', function () {
    Http::fake([AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response(azampayTokenResponse('token-one'), 200)]);

    new AzampayService;
    new AzampayService;
    new AzampayService;

    expect(azampayTokenRequests())->toBe(1);
});

it('requests a token every time when the cache is turned off', function () {
    config()->set('azampay.token_cache_ttl', 0);

    Http::fake([AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response(azampayTokenResponse('token-one'), 200)]);

    new AzampayService;
    new AzampayService;

    expect(azampayTokenRequests())->toBe(2);
});

it('requests a new token after the cached one is forgotten', function () {
    Http::fake([AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response(azampayTokenResponse('token-one'), 200)]);

    (new AzampayService)->forgetToken();
    new AzampayService;

    expect(azampayTokenRequests())->toBe(2);
});

it('does not keep a token past the expiry AzamPay reports', function () {
    Http::fake([
        AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response(
            azampayTokenResponse('short-lived', gmdate('Y-m-d\TH:i:s\Z', time() + 300)),
            200
        ),
    ]);

    new AzampayService;

    $this->travel(200)->seconds();
    new AzampayService;
    expect(azampayTokenRequests())->toBe(1);

    $this->travel(100)->seconds();
    new AzampayService;
    expect(azampayTokenRequests())->toBe(2);
});

it('keeps separate tokens for separate credentials', function () {
    Http::fake([AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response(azampayTokenResponse('token-one'), 200)]);

    new AzampayService;

    config()->set('azampay.clientId', 'another-client');
    new AzampayService;

    expect(azampayTokenRequests())->toBe(2);
});

it('sends auth and checkout requests to configured urls', function () {
    config()->set('azampay.urls.auth', 'http://127.0.0.1:4010');
    config()->set('azampay.urls.checkout', 'http://127.0.0.1:4010/');

    Http::fake([
        '127.0.0.1:4010/AppRegistration/GenerateToken' => Http::response(azampayTokenResponse('sim-token'), 200),
        '127.0.0.1:4010/api/v1/Partner/GetPaymentPartners' => Http::response([['partnerName' => 'Azampesa']], 200),
    ]);

    $partners = (new AzampayService)->getPaymentPartners();

    expect($partners)->toBe([['partnerName' => 'Azampesa']]);

    Http::assertNotSent(fn (Request $request) => str_contains($request->url(), 'azampay.co.tz'));
});

it('renews an expired token inside a long-lived instance', function () {
    Http::fake([
        AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response(
            azampayTokenResponse('short-lived', gmdate('Y-m-d\TH:i:s\Z', time() + 300)),
            200
        ),
        AzampayService::SANDBOX_BASE_URL.'/*' => Http::response([], 200),
    ]);

    $service = new AzampayService;
    $service->getPaymentPartners();
    expect(azampayTokenRequests())->toBe(1);

    $this->travel(400)->seconds();
    $service->getPaymentPartners();
    expect(azampayTokenRequests())->toBe(2);
});
