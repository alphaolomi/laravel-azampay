<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Contracts;

use Illuminate\Http\Request;

/**
 * Decides whether a callback request really comes from AzamPay.
 *
 * Bind your own implementation in the container to replace the default
 * signature check, for example with an IP allow-list or a shared secret.
 */
interface CallbackVerifier
{
    public function verify(Request $request): bool;
}
