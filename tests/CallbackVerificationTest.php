<?php

use Alphaolomi\Azampay\AzampayService;
use Alphaolomi\Azampay\Callbacks\SignatureVerifier;
use Alphaolomi\Azampay\Contracts\CallbackVerifier;
use Alphaolomi\Azampay\Events\AzampayCallback;
use Alphaolomi\Azampay\Events\AzampayDisbursementCallback;
use Alphaolomi\Azampay\Http\CallbackRoutes;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\postJson;

/**
 * A checkout callback as AzamPay sends it, signed with the given private key.
 *
 * @param  array<string, mixed>  $overrides
 * @param  array<int, string>  $fields
 * @return array<string, mixed>
 */
function azampaySignedCallback(mixed $privateKey, array $overrides = [], array $fields = SignatureVerifier::DEFAULT_FIELDS): array
{
    $payload = array_merge([
        'amount' => '15000',
        'message' => 'Payment received',
        'msisdn' => '255700000001',
        'operator' => 'Airtel',
        'reference' => 'ref-001',
        'externalreference' => 'pg-ref-001',
        'transactionstatus' => 'success',
        'utilityref' => 'vendor-ref-001',
    ], $overrides);

    $message = implode('', array_map(fn ($field) => (string) ($payload[$field] ?? ''), $fields));

    openssl_sign($message, $signature, $privateKey, OPENSSL_ALGO_SHA256);

    return $payload + ['signature' => base64_encode($signature)];
}

function azampayPublicKeyUrl(): string
{
    return AzampayService::SANDBOX_BASE_URL.'/azampay/v1/public-key*';
}

beforeEach(function () {
    $this->pair = azampayTestKeyPair();

    if ($this->pair === null) {
        $this->markTestSkipped('OpenSSL cannot generate a key pair in this environment.');
    }

    $auth = json_decode(file_get_contents(__DIR__.'/stubs/responses/generate_token_success.json'), true);

    Http::fake([AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response($auth, 200)]);

    Event::fake([AzampayCallback::class, AzampayDisbursementCallback::class]);
});

it('accepts an unsigned callback while verification is off', function () {
    $payload = azampaySignedCallback($this->pair['private']);
    unset($payload['signature']);

    postJson(route('checkout_payment_callback'), $payload)->assertStatus(200);

    Event::assertDispatched(AzampayCallback::class);
});

it('accepts a callback with a valid signature', function () {
    config()->set('azampay.callback.verify', true);
    config()->set('azampay.callback.public_key', $this->pair['public']);

    postJson(route('checkout_payment_callback'), azampaySignedCallback($this->pair['private']))->assertStatus(200);

    Event::assertDispatched(AzampayCallback::class);
});

it('rejects a callback whose signed fields were changed', function (string $field, string $value) {
    config()->set('azampay.callback.verify', true);
    config()->set('azampay.callback.public_key', $this->pair['public']);

    $payload = azampaySignedCallback($this->pair['private'], ['transactionstatus' => 'failure']);
    $payload[$field] = $value;

    postJson(route('checkout_payment_callback'), $payload)->assertStatus(403);

    Event::assertNotDispatched(AzampayCallback::class);
})->with([
    'status flipped to success' => ['transactionstatus', 'success'],
    'another order reference' => ['utilityref', 'vendor-ref-999'],
    'another provider reference' => ['externalreference', 'pg-ref-999'],
    'another operator' => ['operator', 'Tigo'],
]);

it('rejects a callback with a missing or malformed signature', function (?string $signature) {
    config()->set('azampay.callback.verify', true);
    config()->set('azampay.callback.public_key', $this->pair['public']);

    $payload = azampaySignedCallback($this->pair['private']);

    if ($signature === null) {
        unset($payload['signature']);
    } else {
        $payload['signature'] = $signature;
    }

    postJson(route('checkout_payment_callback'), $payload)->assertStatus(403);

    Event::assertNotDispatched(AzampayCallback::class);
})->with([
    'missing' => [null],
    'empty' => [''],
    'not base64' => ['***'],
    'random bytes' => [base64_encode('not a signature')],
]);

it('rejects a callback signed with another key', function () {
    $other = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA] + (is_file(dirname(PHP_BINARY).'/extras/ssl/openssl.cnf') && ! getenv('OPENSSL_CONF') ? ['config' => dirname(PHP_BINARY).'/extras/ssl/openssl.cnf'] : []));

    config()->set('azampay.callback.verify', true);
    config()->set('azampay.callback.public_key', $this->pair['public']);

    postJson(route('checkout_payment_callback'), azampaySignedCallback($other))->assertStatus(403);

    Event::assertNotDispatched(AzampayCallback::class);
});

it('fetches the public key from AzamPay once and reuses it', function () {
    config()->set('azampay.callback.verify', true);

    Http::fake([azampayPublicKeyUrl() => Http::response(['success' => true, 'format' => 'Pem', 'publicKey' => $this->pair['public']], 200)]);

    postJson(route('checkout_payment_callback'), azampaySignedCallback($this->pair['private']))->assertStatus(200);
    postJson(route('checkout_payment_callback'), azampaySignedCallback($this->pair['private'], ['utilityref' => 'vendor-ref-002']))->assertStatus(200);

    expect(Http::recorded(fn (ClientRequest $request) => str_contains($request->url(), '/azampay/v1/public-key'))->count())->toBe(1);

    Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), '/azampay/v1/public-key')
        && str_contains($request->url(), 'format=Pem')
        && $request->hasHeader('Authorization'));
});

it('fetches the key again when the cached one no longer matches', function () {
    config()->set('azampay.callback.verify', true);

    $old = azampayTestKeyPair();
    $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA] + (is_file(dirname(PHP_BINARY).'/extras/ssl/openssl.cnf') && ! getenv('OPENSSL_CONF') ? ['config' => dirname(PHP_BINARY).'/extras/ssl/openssl.cnf'] : []);
    $new = openssl_pkey_new($options);
    $newPublic = openssl_pkey_get_details($new)['key'];

    Http::fake([
        azampayPublicKeyUrl() => Http::sequence()
            ->push(['publicKey' => $old['public']], 200)
            ->push(['publicKey' => $newPublic], 200),
    ]);

    postJson(route('checkout_payment_callback'), azampaySignedCallback($old['private']))->assertStatus(200);
    postJson(route('checkout_payment_callback'), azampaySignedCallback($new))->assertStatus(200);

    expect(Http::recorded(fn (ClientRequest $request) => str_contains($request->url(), '/azampay/v1/public-key'))->count())->toBe(2);
});

it('rejects the callback when the public key cannot be fetched', function () {
    config()->set('azampay.callback.verify', true);

    Http::fake([azampayPublicKeyUrl() => Http::response(['message' => 'error'], 500)]);

    postJson(route('checkout_payment_callback'), azampaySignedCallback($this->pair['private']))->assertStatus(403);

    Event::assertNotDispatched(AzampayCallback::class);
});

it('accepts a public key given without PEM headers', function () {
    $body = trim(str_replace(['-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----', "\n", "\r"], '', $this->pair['public']));

    config()->set('azampay.callback.verify', true);
    config()->set('azampay.callback.public_key', $body);

    postJson(route('checkout_payment_callback'), azampaySignedCallback($this->pair['private']))->assertStatus(200);
});

it('signs over the configured fields', function () {
    config()->set('azampay.callback.verify', true);
    config()->set('azampay.callback.public_key', $this->pair['public']);
    config()->set('azampay.callback.signature_fields', ['utilityref', 'externalreference']);

    $payload = azampaySignedCallback($this->pair['private'], [], ['utilityref', 'externalreference']);

    postJson(route('checkout_payment_callback'), $payload)->assertStatus(200);
    postJson(route('checkout_payment_callback'), azampaySignedCallback($this->pair['private']))->assertStatus(403);
});

it('uses a custom verifier bound in the container', function () {
    config()->set('azampay.callback.verify', true);

    app()->bind(CallbackVerifier::class, fn () => new class implements CallbackVerifier
    {
        public function verify(Request $request): bool
        {
            return $request->header('X-Shared-Secret') === 'let-me-in';
        }
    });

    $payload = azampaySignedCallback($this->pair['private']);

    postJson(route('checkout_payment_callback'), $payload)->assertStatus(403);
    postJson(route('checkout_payment_callback'), $payload, ['X-Shared-Secret' => 'let-me-in'])->assertStatus(200);
});

it('registers the checkout callback on a configured path', function () {
    config()->set('azampay.callback.path', '/hooks/azampay/checkout');

    CallbackRoutes::register();

    postJson('/hooks/azampay/checkout', azampaySignedCallback($this->pair['private']))->assertStatus(200);

    Event::assertDispatched(AzampayCallback::class);
});

it('does not register the disbursement callback unless it is turned on', function () {
    postJson(CallbackRoutes::DISBURSEMENT_PATH, ['pgReferenceId' => 'pg-123', 'status' => 'success'])->assertStatus(404);
});

it('dispatches an event for a disbursement callback when turned on', function () {
    config()->set('azampay.callback.enabled', false);
    config()->set('azampay.callback.disbursement_enabled', true);

    CallbackRoutes::register();

    postJson(CallbackRoutes::DISBURSEMENT_PATH, [
        'initiatorReferenceId' => 'WD20261121000001',
        'pgReferenceId' => 'pg-123',
        'fspReferenceId' => 'fsp-9',
        'amount' => '15000',
        'status' => 'success',
        'message' => 'Transfer completed',
        'operator' => 'Tigo',
    ])->assertStatus(200);

    Event::assertDispatched(AzampayDisbursementCallback::class, fn ($event) => $event->data['pgReferenceId'] === 'pg-123');

    postJson(CallbackRoutes::DISBURSEMENT_PATH, ['status' => 'success'])->assertStatus(422);
});

it('fetches the key again at most once per interval when signatures keep failing', function () {
    config()->set('azampay.callback.verify', true);

    Http::fake([azampayPublicKeyUrl() => Http::response(['publicKey' => $this->pair['public']], 200)]);

    $forged = azampaySignedCallback($this->pair['private']);
    $forged['transactionstatus'] = 'failure';

    foreach (range(1, 5) as $attempt) {
        postJson(route('checkout_payment_callback'), $forged)->assertStatus(403);
    }

    // one normal fetch, one forced refresh, then none until the interval passes
    expect(Http::recorded(fn (ClientRequest $request) => str_contains($request->url(), '/azampay/v1/public-key'))->count())->toBe(2);

    $this->travel(301)->seconds();
    postJson(route('checkout_payment_callback'), $forged)->assertStatus(403);

    expect(Http::recorded(fn (ClientRequest $request) => str_contains($request->url(), '/azampay/v1/public-key'))->count())->toBe(3);
});

it('only accepts callbacks from allowed addresses when a list is set', function () {
    config()->set('azampay.callback.allowed_ips', ['203.0.113.10', '198.51.100.0/24']);

    $payload = azampaySignedCallback($this->pair['private']);

    postJson(route('checkout_payment_callback'), $payload)->assertStatus(403);
    Event::assertNotDispatched(AzampayCallback::class);

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7']);
    postJson(route('checkout_payment_callback'), $payload)->assertStatus(200);

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10']);
    postJson(route('checkout_payment_callback'), $payload)->assertStatus(200);
});

it('guards the disbursement callback with the allowed addresses and a custom verifier', function () {
    config()->set('azampay.callback.enabled', false);
    config()->set('azampay.callback.disbursement_enabled', true);
    config()->set('azampay.callback.disbursement_path', '/hooks/azampay/transfers');

    CallbackRoutes::register();

    $payload = ['pgReferenceId' => 'pg-123', 'status' => 'success'];

    config()->set('azampay.callback.allowed_ips', ['203.0.113.10']);
    postJson('/hooks/azampay/transfers', $payload)->assertStatus(403);
    Event::assertNotDispatched(AzampayDisbursementCallback::class);

    config()->set('azampay.callback.allowed_ips', []);
    config()->set('azampay.callback.verify', true);

    // the built-in signature check does not apply to disbursement callbacks
    postJson('/hooks/azampay/transfers', $payload)->assertStatus(200);

    app()->bind(CallbackVerifier::class, fn () => new class implements CallbackVerifier
    {
        public function verify(Request $request): bool
        {
            return $request->header('X-Shared-Secret') === 'let-me-in';
        }
    });

    postJson('/hooks/azampay/transfers', $payload)->assertStatus(403);
    postJson('/hooks/azampay/transfers', $payload, ['X-Shared-Secret' => 'let-me-in'])->assertStatus(200);
});
