<p align="center">
    <img src="/art/azampay-logo.png" width="300" title="Azampay Logo" alt="Azampay Logo">
</p>

<div align="center"><h1>Laravel Azampay</h1>

<a href="https://packagist.org/packages/alphaolomi/laravel-azampay">
    <img src="https://img.shields.io/packagist/v/alphaolomi/laravel-azampay.svg?style=flat-square" alt="Latest Version on Packagist">
</a>

<a href="https://github.com/alphaolomi/laravel-azampay/actions/workflows/tests.yml">
    <img src="https://github.com/alphaolomi/laravel-azampay/actions/workflows/tests.yml/badge.svg" alt="GitHub Tests Action Status">
</a>

<a href='https://github.com/alphaolomi/laravel-azampay/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain'>
    <img src="https://github.com/alphaolomi/laravel-azampay/actions/workflows/fix-php-code-style-issues.yml/badge.svg" alt="GitHub Code Style Action Status">
</a>

<a href="https://packagist.org/packages/alphaolomi/laravel-azampay">
    <img src="https://img.shields.io/packagist/dt/alphaolomi/laravel-azampay.svg?style=flat-square" alt="Total Downloads">
</a>

</div>


Laravel Azampay provides an expressive, fluent interface to Azampay's payments services. It handles almost all of the boilerplate payments & billing 
so you don't have to.
 
## Installation

### Prerequisites

- PHP 8.2+
- Laravel 10, 11, 12 or 13
- Composer

You can install the package via Composer:

```bash
composer require alphaolomi/laravel-azampay
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="azampay-config"
```

## Setup

Add the following to your .env file

```bash
AZAMPAY_APP_NAME="your_app_name"
AZAMPAY_CLIENT_ID="your_client_id"
AZAMPAY_CLIENT_SECRET="your_client_secret"
AZAMPAY_ENVIRONMENT="sandbox"
AZAMPAY_TOKEN="Your_Token"
```

Optional settings:

```bash
# Keep the access token in your cache for this many seconds (default 3300, 0 turns it off)
AZAMPAY_TOKEN_CACHE_TTL=3300

# Point the package at other hosts, for example a local simulator
AZAMPAY_AUTH_URL=
AZAMPAY_CHECKOUT_URL=
AZAMPAY_DISBURSEMENT_URL=

# Change a disbursement endpoint path (relative to the disbursement URL)
AZAMPAY_NAME_LOOKUP_PATH=
AZAMPAY_DISBURSE_PATH=
AZAMPAY_TRANSACTION_STATUS_PATH=
```

The access token is cached and reused across requests, and renewed when it expires. Call `Azampay::forgetToken()` to drop it early.

## Usage

## Checkout API

### MNO Checkout

Mobile checkout using `AzampayService` Class

> NOTE: Configs are loaded automatically.

```php
use Alphaolomi\Azampay\AzampayService;

$azampay = new AzampayService();

$data = $azampay->mobileCheckout([
    'amount' => 1000,
    'currency' => 'TZS',
    'accountNumber' => '0625933171',
    'externalId' => '08012345678',
    'provider' => 'Mpesa',
]);
```

### Response

```
array:3 [▼
  "success" => true
  "transactionId" => "b85e971981844a6f8888b42579655b8f"
  "message" => "Your request has been received and is being processed."
]
```

MNO checkout using Facade Class

```php
use Alphaolomi\Azampay\Facades\Azampay;

$data = Azampay::mobileCheckout([
    'amount' => 1000,
    'currency' => 'TZS',
    'accountNumber' => '0625933171',
    'externalId' => '08012345678',
    'provider' => 'Mpesa',
]);
```

### Response

```
array:3 [▼
  "success" => true
  "transactionId" => "b85e971981844a6f8888b42579655b8f"
  "message" => "Your request has been received and is being processed."
]
```

### Bank Checkout

Bank checkout using AzampayService Class

```php
use Alphaolomi\Azampay\AzampayService;

$azampay = new AzampayService();

$data = $azampay->bankCheckout([
        'amount' => 1000,
        'currencyCode' => 'TZS',
        'merchantAccountNumber' => '34567890987654',
        'merchantMobileNumber' => '08012345678',
        'merchantName' => 'alphaolomi@gmail.com',
        'otp' => '1234',
        'provider' => 'CRDB',
        'referenceId' => '24345345',
    ]);
```

### Response

```php
array:3 [▼
  "success" => true
  "transactionId" => "b85e971981844a6f8888b42579655b8f"
  "message" => "Your request has been received and is being processed."
]
```

Bank checkout using Facade Class

```php
use Alphaolomi\Azampay\Facades\Azampay;

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

# Response
[ 'transactionId' => 'string', 'message' => 'string' ]

```

### Get payment partners

Get payment partners using AzampayService Class

```php
use Alphaolomi\Azampay\AzampayService;

$azampay = new AzampayService();

$data = $azampay->getPaymentPartners();

# Response
[
    [
        'id' => "6ebafc56-6d4d-4265-a8d4-c0e1e7806c19",
        'logoUrl' => 'https://azampay-sarafutest.s3.eu-central-1.amazonaws.com/azampesa.png',
        'partnerName' => 'Azampesa',
        'provider' => 5,
        'vendorName' => 'string',
        'paymentVendorId' => '1213c943-b30e-4c9e-ac2f-d34796f01d2d',
        'paymentPartnerId' => '70cd6bba-7f81-4ac8-9276-d5c0a189f2d4',
        "paymentAcknowledgmentRoute": "url",
        'currency' => 'TZS',
        "status" =>  "1",
        "vendorType" =>  "seller"
    ],
    // ...
]
```

Get payment partners using Facade Class

```php
use Alphaolomi\Azampay\Facades\Azampay;

$data = Azampay::getPaymentPartners();

# Response
[
    [
        'id' => "6ebafc56-6d4d-4265-a8d4-c0e1e7806c19",
        'logoUrl' => 'https://azampay-sarafutest.s3.eu-central-1.amazonaws.com/azampesa.png',
        'partnerName' => 'Azampesa',
        'provider' => 5,
        'vendorName' => 'string',
        'paymentVendorId' => '1213c943-b30e-4c9e-ac2f-d34796f01d2d',
        'paymentPartnerId' => '70cd6bba-7f81-4ac8-9276-d5c0a189f2d4',
        "paymentAcknowledgmentRoute": "url",
        'currency' => 'TZS',
        "status" =>  "1",
        "vendorType" =>  "seller"
    ],
    // ...
]
```

### Post Checkout

Post checkout using AzampayService Class

```php
use Alphaolomi\Azampay\AzampayService;

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

# Response
[ 'transactionId' => 'string', 'message' => 'string' ]


```

Post checkout using Facade Class

```php
use Alphaolomi\Azampay\Facades\Azampay;

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

# Response
'string'

```

### Callback route and event

The packages has a route for handling callback from Azampay. The
route is `/api/v1/Checkout/Callback` and named `checkout_payment_callback`.

Whenever a callback is received from Azampay, the package will
dispatch Event `AzampayCallback::class` . You can create a
listener and do further processing with the callback data which will
be passed when the event get dispatched.

#### Registering the route

This package has a route for handling callback from Azampay. Its enabled by default.

On your `App\Providers\EventServiceProvider` register a listener
for `AzampayCallback::class` event.


```bash
php artisan make:listener AzampayCallbackListener
```


In your `App\Listeners\AzampayCallbackListener` class, add the update the `handle` method to handle the event.

```php
use Alphaolomi\Azampay\Events\AzampayCallback;
// ...

public function handle(AzampayCallback $event)
{
    // Handle the event
    // Example save the callback data to database
    
    // $event->data 
    // See https://developerdocs.azampay.co.tz/redoc#tag/Checkout-API/operation/Callback
    // for callback data structure
    // ...
}

// ...
```


```php
use Alphaolomi\Azampay\Events\AzampayCallback;
use App\Listeners\AzampayCallbackListener;

/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    AzampayCallback::class => [
        AzampayCallbackListener::class,
    ],
];
```

Now, whenever a callback is received from Azampay, the package will call the `handle` method of the `AzampayCallbackListener` class. with the callback data.

### Verifying callbacks

Your callback URL is public. Without a check, anyone who knows it can post a fake "payment succeeded". AzamPay signs each checkout callback, and the package can verify that signature for you:

```bash
AZAMPAY_VERIFY_CALLBACKS=true
```

With this on, a callback whose signature does not match AzamPay's public key gets a `403` response and no `AzampayCallback` event is dispatched. The public key is fetched from AzamPay, cached for a day, and fetched again if a signature stops matching.

To pin the key yourself instead, set it as PEM contents or a file path:

```bash
AZAMPAY_CALLBACK_PUBLIC_KEY="-----BEGIN PUBLIC KEY-----\n...\n-----END PUBLIC KEY-----"
```

Verification is off by default in 1.x and will be on by default in 2.0.

Even with verification on, treat a callback as a signal and make your handler safe to run twice: look the order up by `utilityref`, check the amount, and only then mark it paid.

#### Using your own check

Bind your own `CallbackVerifier` to replace the signature check, for example with a shared secret or an IP allow-list:

```php
use Alphaolomi\Azampay\Contracts\CallbackVerifier;

$this->app->bind(CallbackVerifier::class, MyCallbackVerifier::class);
```

### Callback route options

Set these in `config/azampay.php` under `callback`:

| Key | Default | What it does |
|---|---|---|
| `enabled` | `true` | Register the checkout callback route |
| `path` | `/api/v1/Checkout/Callback` | The route's path |
| `middleware` | `[]` | Extra middleware for the callback routes, for example a rate limiter |
| `allowed_ips` | `[]` | Only accept callbacks from these IP addresses or CIDR ranges |
| `verify` | `false` | Verify the callback signature |
| `signature_fields` | `utilityref`, `externalreference`, `transactionstatus`, `operator` | The fields the signature covers, in order |
| `disbursement_enabled` | `false` | Register the disbursement callback route |
| `disbursement_path` | `/api/v1/Disbursement/Callback` | Its path |

### Disbursement callback

AzamPay can call you with the result of a transfer. Turn the route on, give AzamPay its URL, and listen for the event:

```bash
AZAMPAY_DISBURSEMENT_CALLBACK_ENABLED=true
```

```php
use Alphaolomi\Azampay\Events\AzampayDisbursementCallback;

public function handle(AzampayDisbursementCallback $event)
{
    $pgReferenceId = $event->data['pgReferenceId'];
    $status = $event->data['status']; // "success" or "failure"

    // Confirm before you act on it
    $confirmed = Azampay::disbursement()->transactionStatus($pgReferenceId, $bankName);
}
```

Protect this route before you turn it on:

- Always confirm the result with `transactionStatus()` before you release or refund money. Never act on the callback alone.
- Limit who can call it with `AZAMPAY_CALLBACK_ALLOWED_IPS` (comma separated addresses or CIDR ranges), using the addresses AzamPay calls you from.
- Set `AZAMPAY_DISBURSEMENT_CALLBACK_PATH` to a long random path and share it only with AzamPay.
- With `AZAMPAY_VERIFY_CALLBACKS=true`, a `CallbackVerifier` you bind yourself is applied to this route too.

## Disbursement API

Send money from your AzamPay account to a mobile wallet, with the transfer checksum AzamPay requires.

### Setup

AzamPay issues a public key for the checksum and tells you your source account. Add them to `.env`:

```bash
AZAMPAY_DISBURSEMENT_PUBLIC_KEY="-----BEGIN PUBLIC KEY-----\n...\n-----END PUBLIC KEY-----"
AZAMPAY_SOURCE_ACCOUNT="your_source_account"
AZAMPAY_SOURCE_NAME="Your Registered Business Name"
AZAMPAY_SOURCE_BANK="Azampesa"
```

`AZAMPAY_DISBURSEMENT_PUBLIC_KEY` can also be a path to a PEM file. If you published the config file before v1.2.0, publish it again or add the `urls`, `token_cache_ttl`, `timeout` and `disbursement` keys from the package's `config/azampay.php`.

### Look up a name

Check who owns the wallet before you pay it.

```php
use Alphaolomi\Azampay\Facades\Azampay;

$owner = Azampay::disbursement()->nameLookup('tigo', '255700000001');

// ['name' => 'JOHN DOE', 'fName' => 'JOHN', 'lName' => 'DOE', 'status' => true, ...]
```

### Send a transfer

```php
$transfer = Azampay::disbursement()->disburse(
    destination: [
        'accountNumber' => '255700000001',
        'fullName' => 'John Doe',
        'bankName' => 'tigo',
    ],
    amount: 15000,
    externalReferenceId: 'WD20261121000001',
    options: ['remarks' => 'Wallet withdrawal'],
);

// ['pgReferenceId' => '...', 'message' => 'Your transaction is in process', 'success' => true, 'statusCode' => 200]
```

- `amount` is a whole number in TZS.
- `externalReferenceId` is your own unique reference: letters and digits only, at most 30 characters. AzamPay rejects a reference it has seen before, which protects you from paying twice.
- A successful response means AzamPay accepted the transfer, not that the money has arrived. Store `pgReferenceId` and confirm the outcome with the status call below or the disbursement callback AzamPay sends you.
- Record the transfer as pending in your own database before you call `disburse()`, so a timeout never leaves you unsure whether money moved.

### Check a transfer

```php
$status = Azampay::disbursement()->transactionStatus($transfer['pgReferenceId'], 'tigo');
```

### Errors

The disbursement client throws instead of returning error arrays:

| Exception | When |
|---|---|
| `Exceptions\ValidationException` | The amount, reference or destination is invalid. Nothing was sent. |
| `Exceptions\ConfigurationException` | The public key or source account is missing or invalid. Nothing was sent. |
| `Exceptions\AuthenticationException` | AzamPay rejected the access token (HTTP 401). |
| `Exceptions\RequestException` | AzamPay answered with any other error. `$e->status()` and `$e->json()` hold the details. |

All of them extend `Exceptions\AzampayException`.

```php
use Alphaolomi\Azampay\Exceptions\RequestException;

try {
    $transfer = Azampay::disbursement()->disburse($destination, 15000, $reference);
} catch (RequestException $e) {
    // 400 invalid bank name, 402 request expired, 403 duplicate reference
    report($e);
}
```

### Older methods

`createTransfer()`, `nameLookup()` and `getTransactionStatus()` on the facade still work but are deprecated. They send no checksum. Move to `Azampay::disbursement()`; they will be removed in 2.0.


### Create transfer

Create transfer using AzampayService Class

```php
use Alphaolomi\Azampay\AzampayService;

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

# Response
[
    [
        'data' => 'Transaction successful.',
        'message' => 'Request successful.',
        'success' => true,
        'statusCode' => 200
    ]
]
```

Create transfer using Facade Class

```php
use Alphaolomi\Azampay\Facades\Azampay;

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

# Response
[
    [
        'data' => 'Transaction successful.',
        'message' => 'Request successful.',
        'success' => true,
        'statusCode' => 200
    ]
]

```

### Name lookup

Name lookup using AzampayService Class

```php
use Alphaolomi\Azampay\AzampayService;

$azampay = new AzampayService();

$data = $azampay->nameLookup([
    'bankName' => 'string',
    'accountNumber' => 'string',
]);

# Response
[
    'name' => 'string',
    'message' => 'string',
    'success' => true,
    'accountNumber' => 'string',
    'bankName' => 'string'
]
```

Name lookup using Facade Class

```php
use Alphaolomi\Azampay\Facades\Azampay;

$data = Azampay::nameLookup([
        'bankName' => 'string',
        'accountNumber' => 'string',
    ]);

# Response
[
    'name' => 'string',
    'message' => 'string',
    'success' => true,
    'accountNumber' => 'string',
    'bankName' => 'string'
]

```

### Get transaction status

Get transaction status using AzampayService Class

```php
use Alphaolomi\Azampay\AzampayService;

$azampay = new AzampayService();

$data = $azampay->getTransactionStatus([
    'bankName' => 'CRDB',
    'pgReferenceId' => '10'
]);

# Response
[
    [
        'data': 'Transaction successful.',
        'message': 'Request successful.',
        'success': true,
        'statusCode': 200
    ]
]
```

Get transaction status using Facade Class

```php
use Alphaolomi\Azampay\Facades\Azampay;

$data = Azampay::getTransactionStatus([
    'bankName' => 'CRDB',
    'pgReferenceId' => '10'
]);

# Response
[
    [
        'data': 'Transaction successful.',
        'message': 'Request successful.',
        'success': true,
        'statusCode': 200
    ]
]

```

### Azampay documentation

You can find more details about azampay on their documentation
in this link [Azampay Documentation](https://developerdocs.azampay.co.tz/redoc).

## Testing

```bash
composer test
```

## Versions and support

| Package | Laravel | PHP | Status |
|---|---|---|---|
| 1.x | 10, 11, 12, 13 | 8.2 to 8.5 | Supported |
| 0.x | 9, 10 | 8.1 | Ended |

Releases follow [semantic versioning](https://semver.org): a major version drops a Laravel or PHP version or changes the public API, a minor version adds a Laravel version or a feature, a patch version only fixes. A new Laravel release is supported within 30 days.

## Getting help

- Questions and how-to: [Discussions, Q&A](https://github.com/alphaolomi/laravel-azampay/discussions/categories/q-a). Questions get an answer within a week.
- Ideas and feature requests: [Discussions, Ideas](https://github.com/alphaolomi/laravel-azampay/discussions/categories/ideas).
- Bugs: [open an issue](https://github.com/alphaolomi/laravel-azampay/issues/new/choose).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alpha Olomi](https://github.com/alphaolomi)
- [Omakei](https://github.com/omakei)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Support us

If you find this package useful, you can support us by staring this repository and sharing it with others.
