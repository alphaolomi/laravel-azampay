<?php

namespace Alphaolomi\Azampay\Tests;

use Alphaolomi\Azampay\AzampayServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [AzampayServiceProvider::class];
    }
}
