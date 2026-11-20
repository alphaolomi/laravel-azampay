<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Http\Middleware;

use Alphaolomi\Azampay\Contracts\CallbackVerifier;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rejects callbacks that fail verification, when `azampay.callback.verify` is on.
 */
class VerifyAzampayCallback
{
    public function __construct(private readonly CallbackVerifier $verifier) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (config('azampay.callback.verify') && ! $this->verifier->verify($request)) {
            return response()->json(['message' => 'Invalid callback signature.'], 403);
        }

        return $next($request);
    }
}
