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


Laravel Axampay provides an expressive, fluent interface to Azampay's payments services. It handles almost all of the boilerplate payments & billing 
so you don't have to.
 
## Installation

### Prerequisites

- PHP 8.1+
- Laravel v10+
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

## Disbursement API

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
