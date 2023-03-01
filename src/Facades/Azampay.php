<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Alphaolomi\Azampay\AzampayService setOptions(array $options)
 * @method static \Alphaolomi\Azampay\AzampayService mobileCheckout(array $payload)
 * @method static \Alphaolomi\Azampay\AzampayService bankCheckout(array $payload)
 * @method static \Alphaolomi\Azampay\AzampayService getPaymentPartners()
 * @method static \Alphaolomi\Azampay\AzampayService postCheckout(array $payload)
 * @method static \Alphaolomi\Azampay\AzampayService createTransfer(array $payload)
 * @method static \Alphaolomi\Azampay\AzampayService nameLookup(array $payload)
 * @method static \Alphaolomi\Azampay\AzampayService getTransactionStatus(?array $payload)
 *
 * @author Alpha Olomi and Michael Omakei
 *
 * @see \Alphaolomi\Azampay\Azampay
 */
class Azampay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Alphaolomi\Azampay\AzampayService::class;
    }
}
