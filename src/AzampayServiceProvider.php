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
            ->hasRoute('azampay_api')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        $this->app->bind('azampay', function () {
            $data = config('azampay');
            if (empty($data)) {
                throw new \Exception('Azampay config not found');
            }

            if($data['appName']) {
                throw new \Exception('AppName is required');
            }

            if($data['clientId']) {
                throw new \Exception('ClientId is required');
            }

            if($data['clientSecret']) {
                throw new \Exception('ClientSecret is required');
            }

            // if($data['environment']) {
            //     throw new \Exception('Environment is required');
            // }

            // if($data['token']) {
            //     throw new \Exception('Token is required');
            // }
            return new AzampayService();
        });
    }
}
