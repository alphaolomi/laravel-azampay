<?php

use Alphaolomi\Azampay\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * A throwaway RSA key pair, or null where OpenSSL cannot create one.
 *
 * @return array{public: string, private: string}|null
 */
function azampayTestKeyPair(): ?array
{
    static $pair = false;

    if ($pair === false) {
        $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];

        // PHP on Windows ships its OpenSSL config next to the binary and does not always find it.
        $bundled = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';

        if (! getenv('OPENSSL_CONF') && is_file($bundled)) {
            $options['config'] = $bundled;
        }

        $key = @openssl_pkey_new($options);
        $private = '';

        if ($key === false || ! @openssl_pkey_export($key, $private, null, $options)) {
            $pair = null;
        } else {
            $pair = ['public' => openssl_pkey_get_details($key)['key'], 'private' => $private];
        }
    }

    return $pair;
}
