<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay;

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
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        $this->app->bind('azampay', function () {
            return new AzampayService([
                'appName' => config('azampay.appName'),
                'clientId' => config('azampay.clientId'),
                'clientSecret' => config('azampay.clientSecret'),
                'environment' => config('azampay.environment'),
            ]);
        });
    }
}
