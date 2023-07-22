<?php

use Alphaolomi\Azampay\AzampayService;

it('can instantiate the service', function () {
    $config = [
        'appName' => 'Openpesa',
        'clientId' => '0239aa76-62f0-4479-8e52-2dd62751933b',
        'clientSecret' => "GxBITgYleAzmoZ5Q3CNRJkuJHISfYfZS2GUyy0zkkMph6flN/bKq1Iouum/WBMFUfOOWyE7h5r5vCMQS1mAhuiMHvDVfC43QzoMRJnWDQ50tHufhbVsZUwonIkmusQZRSIS7Q0rMqqi93mJhPi6gIwiVlh8oeQuA9N3EsNr0rStgEhT9doVVjbo65ilufUo118/dr0fWbFGJv2n7Y2xddyLQ5eCO84Ie8wfSSKLBSpK+bKhUC36XtGR/Ocj3arE3CXLkfXYL0ELzrda4wkf4rW4dAlagTD2DqxjJoyOv1/+JOkp9B1TAK1RWaNX0UYWFTTsnoPQjmzjUAQWq+vBiDJA66AV3Yq7yj9Mtw0EmLzRXjsUFdI0PPyHfRgfLBJAv6KyK0ZVHJ4oKbOul0qhFeocmyPF8UT7Ps1f1FV04xMKWtibROdGHcYW20SGPhU6UIBJd+9wj5CXYklWtw4p4MsA/AdN2+kjjqfSrwu3H88zqukD+TkiyGVxv/kD3tidlcfOVL8JYuc7std3Pd8VKt8RqcKHijUF6djOM9BiuGUoIiEyb8pRMiBc5HMkemCO2nXPV6uy4CjAD2bZg+KN6dmNorQ1jiOHWKTK7gCO3GlRoj9PZi2EJdXNuTUYvEJcFJBpL4mFBH81ujKAIlpZbCPlKb5wa8Ux6oJuntQ5EJi4="
    ];
    $service = new AzampayService($config);
    expect($service)->toBeInstanceOf(AzampayService::class);
});


it('can make mobile checkout', function () {
    $config = [
        'appName' => 'Openpesa',
        'clientId' => '0239aa76-62f0-4479-8e52-2dd62751933b',
        'clientSecret' => "GxBITgYleAzmoZ5Q3CNRJkuJHISfYfZS2GUyy0zkkMph6flN/bKq1Iouum/WBMFUfOOWyE7h5r5vCMQS1mAhuiMHvDVfC43QzoMRJnWDQ50tHufhbVsZUwonIkmusQZRSIS7Q0rMqqi93mJhPi6gIwiVlh8oeQuA9N3EsNr0rStgEhT9doVVjbo65ilufUo118/dr0fWbFGJv2n7Y2xddyLQ5eCO84Ie8wfSSKLBSpK+bKhUC36XtGR/Ocj3arE3CXLkfXYL0ELzrda4wkf4rW4dAlagTD2DqxjJoyOv1/+JOkp9B1TAK1RWaNX0UYWFTTsnoPQjmzjUAQWq+vBiDJA66AV3Yq7yj9Mtw0EmLzRXjsUFdI0PPyHfRgfLBJAv6KyK0ZVHJ4oKbOul0qhFeocmyPF8UT7Ps1f1FV04xMKWtibROdGHcYW20SGPhU6UIBJd+9wj5CXYklWtw4p4MsA/AdN2+kjjqfSrwu3H88zqukD+TkiyGVxv/kD3tidlcfOVL8JYuc7std3Pd8VKt8RqcKHijUF6djOM9BiuGUoIiEyb8pRMiBc5HMkemCO2nXPV6uy4CjAD2bZg+KN6dmNorQ1jiOHWKTK7gCO3GlRoj9PZi2EJdXNuTUYvEJcFJBpL4mFBH81ujKAIlpZbCPlKb5wa8Ux6oJuntQ5EJi4="
    ];
    $azampay = new AzampayService($config);
    $data = $azampay->mobileCheckout([
        'amount' => 1000,
        'currency' => 'TZS',
        'accountNumber' => '0625933171',
        'externalId' => '08012345678',
        'provider' => 'Mpesa',
    ]);


    expect($data)->dd();
    // expect($data)->toBeArray();
});
