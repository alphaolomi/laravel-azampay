<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Callbacks;

use Alphaolomi\Azampay\AzampayService;
use Alphaolomi\Azampay\Contracts\CallbackVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Verifies the RSA signature AzamPay sends with a checkout callback.
 *
 * The signature is Base64 encoded, made with SHA-256 and PKCS#1 v1.5 over the
 * callback fields listed in `azampay.callback.signature_fields`, joined in
 * that order with no separator.
 */
class SignatureVerifier implements CallbackVerifier
{
    public const DEFAULT_FIELDS = ['utilityref', 'externalreference', 'transactionstatus', 'operator'];

    public function verify(Request $request): bool
    {
        $signature = base64_decode((string) $request->input('signature'), true);

        if ($signature === false || $signature === '') {
            return false;
        }

        $message = $this->message($request);

        $configured = config('azampay.callback.public_key');

        if (is_string($configured) && trim($configured) !== '') {
            return $this->matches($message, $signature, $configured);
        }

        try {
            if ($this->matches($message, $signature, $this->service()->callbackPublicKey())) {
                return true;
            }

            // AzamPay may have rotated its key: fetch it again before rejecting, but
            // not more than once per interval, so forged callbacks cannot make this
            // application call AzamPay on every request.
            $interval = max(1, (int) config('azampay.callback.public_key_refresh_interval', 300));

            if (! Cache::add('azampay:callback-public-key:refreshed', true, $interval)) {
                return false;
            }

            return $this->matches($message, $signature, $this->service()->callbackPublicKey(true));
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    /**
     * The string AzamPay signed.
     */
    public function message(Request $request): string
    {
        $fields = (array) config('azampay.callback.signature_fields', self::DEFAULT_FIELDS);

        return implode('', array_map(
            fn ($field) => (string) $request->input((string) $field, ''),
            $fields === [] ? self::DEFAULT_FIELDS : $fields
        ));
    }

    private function matches(string $message, string $signature, string $publicKey): bool
    {
        $key = openssl_pkey_get_public($this->normalizeKey($publicKey));

        if ($key === false) {
            return false;
        }

        return openssl_verify($message, $signature, $key, OPENSSL_ALGO_SHA256) === 1;
    }

    private function normalizeKey(string $publicKey): string
    {
        $publicKey = trim($publicKey);

        if (! str_contains($publicKey, '-----BEGIN')) {
            if (is_file($publicKey)) {
                return trim((string) file_get_contents($publicKey));
            }

            // A bare Base64 key body: wrap it as a PEM public key.
            $body = preg_replace('/\s+/', '', str_replace('\n', '', $publicKey));

            return "-----BEGIN PUBLIC KEY-----\n".chunk_split((string) $body, 64, "\n").'-----END PUBLIC KEY-----';
        }

        return str_replace('\n', "\n", $publicKey);
    }

    private function service(): AzampayService
    {
        return app(AzampayService::class);
    }
}
