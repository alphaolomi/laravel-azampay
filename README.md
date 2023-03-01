<p align="center">
    <img src="/art/azampay-logo.png" width="300" title="Azampay Logo" alt="Azampay Logo">
</p>

<div align="center"><h1>Laravel Azampay</h1><a href="https://packagist.org/packages/alphaolomi/laravel-azampay">
<img src="https://img.shields.io/packagist/v/alphaolomi/laravel-azampay.svg?style=flat-square" alt="Latest Version on Packagist">
</a><a href="https://github.com/alphaolomi/laravel-azampay/actions?query=workflow%3Arun-tests+branch%3Amain">
<img src="https://img.shields.io/github/workflow/status/alphaolomi/laravel-azampay/run-tests?label=tests" alt="GitHub Tests Action Status">
</a><a href='https://github.com/alphaolomi/laravel-azampay/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain'>
<img src="https://img.shields.io/github/workflow/status/alphaolomi/laravel-azampay/Fix%20PHP%20code%20style%20issues?label=code%20style" alt=GitHub Code Style Action Status">
</a><a href="https://packagist.org/packages/alphaolomi/laravel-azampay">
<img src="https://img.shields.io/packagist/dt/alphaolomi/laravel-azampay.svg?style=flat-square" alt="Total Downloads">
</a>
</div>

## Installation

You can install the package via composer:

```bash
composer require alphaolomi/laravel-azampay
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="azampay-config"
```

## Usage

Using Service manager

```php
use Alphaolomi\Azampay\AzampayService;

$azampay = new Alphaolomi\AzampayService([
    'appName' => 'Mangi shop',
    'clientId' => '',
    'clientSecret' => ''
]);

$data = $azampay->mobileCheckout([
    'amount' => 1_000,
    'phone' => '08012345678',
    'email' => 'alphaolomi@gmail.com'
]);

print_r($data);
```

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

-   [Alpha Olomi](https://github.com/alphaolomi)
-   [omakei](https://github.com/omakei)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
