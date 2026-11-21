<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Support;

use Alphaolomi\Azampay\Exceptions\ConfigurationException;

/**
 * Checksum for AzamPay disbursement requests.
 *
 * AzamPay defines it as Base64(RSA(SHA512(string))): the SHA-512 digest of the
 * input string, encrypted with the public key AzamPay issues to the merchant
 * (PKCS#1 v1.5 padding), then Base64 encoded.
 */
final class Checksum
{
    /**
     * The string the checksum is computed over.
     */
    public static function inputString(
        string $sourceAccount,
        string $destinationAccount,
        string $currency,
        int $amount,
        int $dateInEpoch,
        string $externalReferenceId
    ): string {
        return trim($sourceAccount)
            .trim($destinationAccount)
            .trim($currency)
            .$amount
            .$dateInEpoch
            .trim($externalReferenceId);
    }

    /**
     * @param  string  $publicKey  PEM contents, PEM with literal "\n" sequences, or a path to a PEM file
     */
    public static function make(
        string $publicKey,
        string $sourceAccount,
        string $destinationAccount,
        string $currency,
        int $amount,
        int $dateInEpoch,
        string $externalReferenceId
    ): string {
        $key = openssl_pkey_get_public(self::normalizeKey($publicKey));

        if ($key === false) {
            throw new ConfigurationException('The AzamPay disbursement public key is not a valid PEM public key.');
        }

        $digest = hash('sha512', self::inputString(
            $sourceAccount,
            $destinationAccount,
            $currency,
            $amount,
            $dateInEpoch,
            $externalReferenceId
        ), true);

        $encrypted = '';

        if (! openssl_public_encrypt($digest, $encrypted, $key, OPENSSL_PKCS1_PADDING)) {
            throw new ConfigurationException('The AzamPay disbursement checksum could not be encrypted with the configured public key.');
        }

        return base64_encode($encrypted);
    }

    private static function normalizeKey(string $publicKey): string
    {
        $publicKey = trim($publicKey);

        if ($publicKey === '') {
            throw new ConfigurationException('The AzamPay disbursement public key is not configured (azampay.disbursement.public_key).');
        }

        if (! str_contains($publicKey, '-----BEGIN') && is_file($publicKey)) {
            $publicKey = (string) file_get_contents($publicKey);
        }

        return trim(str_replace('\n', "\n", $publicKey));
    }
}
