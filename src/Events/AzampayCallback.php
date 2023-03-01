<?php

namespace Alphaolomi\Azampay\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AzampayCallback
{
    use Dispatchable;

    public function __construct(public array $data)
    {
    }
}
