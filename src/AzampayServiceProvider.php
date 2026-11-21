<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay;

use Alphaolomi\Azampay\Callbacks\SignatureVerifier;
use Alphaolomi\Azampay\Contracts\CallbackVerifier;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AzampayServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-azampay')
            ->hasRoute('azampay_api')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        $this->app->bind('azampay', function () {
            return new AzampayService;
        });

        $this->app->bindIf(CallbackVerifier::class, SignatureVerifier::class);
    }
}
