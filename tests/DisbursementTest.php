<?php

use Alphaolomi\Azampay\AzampayService;
use Alphaolomi\Azampay\Disbursement;
use Alphaolomi\Azampay\Exceptions\AuthenticationException;
use Alphaolomi\Azampay\Exceptions\ConfigurationException;
use Alphaolomi\Azampay\Exceptions\RequestException;
use Alphaolomi\Azampay\Exceptions\ValidationException;
use Alphaolomi\Azampay\Facades\Azampay;
use Alphaolomi\Azampay\Support\Checksum;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

function azampayDisbursementBase(): string
{
    return AzampayService::SANDBOX_DISBURSEMENT_BASE_URL.'/api/v1/azampay';
}

beforeEach(function () {
    $auth = json_decode(file_get_contents(__DIR__.'/stubs/responses/generate_token_success.json'), true);

    Http::fake([
        AzampayService::SANDBOX_AUTH_BASE_URL.'/*' => Http::response($auth, 200),
    ]);

    config()->set('azampay.disbursement.source', [
        'accountNumber' => '1000000164',
        'fullName' => 'Example Merchant Limited',
        'bankName' => 'Azampesa',
        'countryCode' => 'TZ',
        'currency' => 'TZS',
    ]);
});

it('looks up a name on the disbursement host', function () {
    $stub = ['name' => 'JOHN DOE', 'fName' => 'JOHN', 'lName' => 'DOE', 'status' => true, 'accountNumber' => '255700000001', 'bankName' => 'tigo'];

    Http::fake([azampayDisbursementBase().'/namelookup' => Http::response($stub, 200)]);

    $data = Azampay::disbursement()->nameLookup('tigo', '255700000001');

    expect($data)->toBe($stub);

    Http::assertSent(fn (Request $request) => $request->url() === azampayDisbursementBase().'/namelookup'
        && $request->method() === 'POST'
        && $request['bankName'] === 'tigo'
        && $request['accountNumber'] === '255700000001'
        && $request->hasHeader('Authorization'));
});

it('sends a transfer with a checksum AzamPay can verify', function () {
    $pair = azampayTestKeyPair();

    if ($pair === null) {
        $this->markTestSkipped('OpenSSL cannot generate a key pair in this environment.');
    }

    config()->set('azampay.disbursement.public_key', $pair['public']);

    $stub = ['pgReferenceId' => 'pg-123', 'message' => 'Your transaction is in process', 'success' => true, 'statusCode' => 200];

    Http::fake([azampayDisbursementBase().'/disburse' => Http::response($stub, 200)]);

    $data = Azampay::disbursement()->disburse(
        ['accountNumber' => '255700000001', 'fullName' => 'John Doe', 'bankName' => 'tigo'],
        15000,
        'WD20261121000001',
        ['remarks' => 'Wallet withdrawal', 'dateInEpoch' => 1795000000]
    );

    expect($data)->toBe($stub);

    Http::assertSent(function (Request $request) use ($pair) {
        if ($request->url() !== azampayDisbursementBase().'/disburse') {
            return false;
        }

        $decrypted = '';
        openssl_private_decrypt(base64_decode($request['checksum']), $decrypted, $pair['private'], OPENSSL_PKCS1_PADDING);

        $expected = hash('sha512', '1000000164'.'255700000001'.'TZS'.'15000'.'1795000000'.'WD20261121000001', true);

        return $decrypted === $expected
            && $request['source']['accountNumber'] === '1000000164'
            && $request['destination'] === [
                'countryCode' => 'TZ',
                'fullName' => 'John Doe',
                'bankName' => 'tigo',
                'accountNumber' => '255700000001',
                'currency' => 'TZS',
            ]
            && $request['transferDetails'] === ['type' => 'FUND', 'amount' => 15000, 'dateInEpoch' => 1795000000]
            && $request['externalReferenceId'] === 'WD20261121000001'
            && $request['remarks'] === 'Wallet withdrawal'
            && $request->header('X-Epoch-Date') === ['1795000000'];
    });
});

it('builds the checksum input in the order AzamPay expects', function () {
    expect(Checksum::inputString(' 1000000164 ', '255700000001', 'TZS', 15000, 1795000000, 'REF1'))
        ->toBe('1000000164255700000001TZS150001795000000REF1');
});

it('accepts the public key with escaped line breaks', function () {
    $pair = azampayTestKeyPair();

    if ($pair === null) {
        $this->markTestSkipped('OpenSSL cannot generate a key pair in this environment.');
    }

    $escaped = str_replace("\n", '\n', trim($pair['public']));

    expect(Checksum::make($escaped, '1', '2', 'TZS', 1000, 1795000000, 'REF1'))->toBeString()->not->toBeEmpty();
});

it('refuses a transfer when the public key is missing or invalid', function (?string $key) {
    config()->set('azampay.disbursement.public_key', $key);

    Azampay::disbursement()->disburse(
        ['accountNumber' => '255700000001', 'fullName' => 'John Doe', 'bankName' => 'tigo'],
        15000,
        'REF1'
    );
})->with([null, 'not a key'])->throws(ConfigurationException::class);

it('refuses a transfer when the source account is not configured', function () {
    config()->set('azampay.disbursement.source.accountNumber', null);

    Azampay::disbursement()->disburse(
        ['accountNumber' => '255700000001', 'fullName' => 'John Doe', 'bankName' => 'tigo'],
        15000,
        'REF1'
    );
})->throws(ConfigurationException::class);

it('refuses invalid amounts and references before calling AzamPay', function (mixed $amount, string $reference) {
    try {
        Azampay::disbursement()->disburse(
            ['accountNumber' => '255700000001', 'fullName' => 'John Doe', 'bankName' => 'tigo'],
            $amount,
            $reference
        );
    } finally {
        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/disburse'));
    }
})->with([
    'zero' => [0, 'REF1'],
    'negative' => [-500, 'REF1'],
    'fraction' => [1000.50, 'REF1'],
    'not a number' => ['abc', 'REF1'],
    'reference with a dash' => [1000, 'REF-1'],
    'reference with an underscore' => [1000, 'REF_1'],
    'reference too long' => [1000, str_repeat('A', 31)],
    'empty reference' => [1000, ''],
])->throws(ValidationException::class);

it('refuses a transfer without a destination account', function () {
    Azampay::disbursement()->disburse(['fullName' => 'John Doe', 'bankName' => 'tigo'], 1000, 'REF1');
})->throws(ValidationException::class);

it('gets the status of a transfer', function () {
    $stub = ['data' => 'Transaction successful.', 'message' => 'Transaction Status successfuly fetch', 'success' => true, 'statusCode' => 200];

    Http::fake([azampayDisbursementBase().'/transactionstatus*' => Http::response($stub, 200)]);

    $data = Azampay::disbursement()->transactionStatus('pg-123', 'tigo');

    expect($data)->toBe($stub);

    Http::assertSent(fn (Request $request) => $request->method() === 'GET'
        && str_starts_with($request->url(), azampayDisbursementBase().'/transactionstatus?')
        && str_contains($request->url(), 'pgReferenceId=pg-123')
        && str_contains($request->url(), 'bankName=tigo'));
});

it('throws an authentication exception when the token is rejected', function () {
    Http::fake([azampayDisbursementBase().'/namelookup' => Http::response(['message' => 'Please Provide Valid Authorization', 'status' => 'Error'], 401)]);

    Azampay::disbursement()->nameLookup('tigo', '255700000001');
})->throws(AuthenticationException::class);

it('throws a request exception that carries the error response', function () {
    Http::fake([azampayDisbursementBase().'/transactionstatus*' => Http::response(['message' => 'Transaction not found', 'success' => false], 404)]);

    try {
        Azampay::disbursement()->transactionStatus('missing', 'tigo');
        $this->fail('Expected a RequestException.');
    } catch (RequestException $e) {
        expect($e->status())->toBe(404);
        expect($e->json()['message'])->toBe('Transaction not found');
    }
});

it('uses the configured disbursement url', function () {
    config()->set('azampay.urls.disbursement', 'http://127.0.0.1:4010/');

    Http::fake(['127.0.0.1:4010/api/v1/azampay/namelookup' => Http::response(['status' => true], 200)]);

    $disbursement = (new AzampayService)->disbursement();

    expect($disbursement)->toBeInstanceOf(Disbursement::class);
    expect($disbursement->nameLookup('tigo', '255700000001'))->toBe(['status' => true]);
});

it('uses configured endpoint paths', function () {
    config()->set('azampay.urls.disbursement', 'http://127.0.0.1:4010');
    config()->set('azampay.disbursement.paths.name_lookup', '/v2/lookup/');

    Http::fake([
        '127.0.0.1:4010/v2/lookup' => Http::response(['status' => true], 200),
        '127.0.0.1:4010/api/v1/azampay/transactionstatus*' => Http::response(['success' => true], 200),
    ]);

    $disbursement = (new AzampayService)->disbursement();

    expect($disbursement->nameLookup('tigo', '255700000001'))->toBe(['status' => true]);
    expect($disbursement->transactionStatus('pg-123', 'tigo'))->toBe(['success' => true]);
});
