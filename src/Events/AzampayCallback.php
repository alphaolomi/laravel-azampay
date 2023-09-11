<?php

namespace Alphaolomi\Azampay\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AzampayCallback
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @see https://laravel.com/docs/8.x/events#defining-events
     * @see https://developerdocs.azampay.co.tz/redoc#tag/Checkout-API/operation/Callback
     *
     * @return void
     */
    public function __construct(public array $data)
    {
    }
}
