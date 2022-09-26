<?php

namespace Alphaolomi\Azampay\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Alphaolomi\Azampay\AzampayServiceProvider;

class TestCase extends Orchestra
{

    protected function getPackageProviders($app)
    {
        return [AzampayServiceProvider::class];
    }
}
