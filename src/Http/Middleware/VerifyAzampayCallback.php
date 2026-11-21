<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Http\Middleware;

use Alphaolomi\Azampay\Callbacks\SignatureVerifier;
use Alphaolomi\Azampay\Contracts\CallbackVerifier;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the callback routes.
 *
 * - `azampay.callback.allowed_ips`: when set, only these addresses or ranges may call.
 * - `azampay.callback.verify`: when on, the callback must pass the bound CallbackVerifier.
 *
 * The built-in verifier checks the signature of checkout callbacks. Disbursement
 * callbacks are checked by a verifier you bind yourself.
 */
class VerifyAzampayCallback
{
    public function __construct(private readonly CallbackVerifier $verifier) {}

    public function handle(Request $request, Closure $next, string $type = 'checkout'): Response
    {
        $allowed = array_filter((array) config('azampay.callback.allowed_ips', []));

        if ($allowed !== [] && ! IpUtils::checkIp((string) $request->ip(), array_values($allowed))) {
            return $this->reject('Callback source is not allowed.');
        }

        if (config('azampay.callback.verify') && $this->applies($type) && ! $this->verifier->verify($request)) {
            return $this->reject('Invalid callback signature.');
        }

        return $next($request);
    }

    private function applies(string $type): bool
    {
        // The built-in signature check covers checkout callbacks only.
        return $type === 'checkout' || ! $this->verifier instanceof SignatureVerifier;
    }

    private function reject(string $message): Response
    {
        return response()->json(['message' => $message], 403);
    }
}
