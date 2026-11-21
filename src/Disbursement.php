<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay;

use Alphaolomi\Azampay\Exceptions\AuthenticationException;
use Alphaolomi\Azampay\Exceptions\ConfigurationException;
use Alphaolomi\Azampay\Exceptions\RequestException;
use Alphaolomi\Azampay\Exceptions\ValidationException;
use Alphaolomi\Azampay\Support\Checksum;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Client for the AzamPay disbursement API.
 *
 * Disbursement has its own base URL and endpoint paths, both configurable,
 * and transfers carry a checksum.
 *
 * @see https://developerdocs.azampay.co.tz
 */
class Disbursement
{
    /**
     * Default endpoint paths, relative to the disbursement base URL.
     */
    public const PATHS = [
        'name_lookup' => 'api/v1/azampay/namelookup',
        'disburse' => 'api/v1/azampay/disburse',
        'transaction_status' => 'api/v1/azampay/transactionstatus',
    ];

    /**
     * @param  array<string, mixed>  $config  The `azampay.disbursement` configuration
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $token,
        private readonly array $config = [],
        private readonly int $timeout = 60
    ) {}

    /**
     * Look up the name registered on a wallet or account before paying it.
     *
     * @return array<string, mixed> e.g. name, fName, lName, status, accountNumber, bankName
     *
     * @throws RequestException
     */
    public function nameLookup(string $bankName, string $accountNumber): array
    {
        $response = $this->request()->post($this->url('name_lookup'), [
            'bankName' => $bankName,
            'accountNumber' => $accountNumber,
        ]);

        return $this->decode($response);
    }

    /**
     * Send money from the merchant account to a wallet or account.
     *
     * A successful response means AzamPay accepted the transfer, not that the
     * money has arrived. Keep `pgReferenceId` and confirm the outcome with
     * transactionStatus() or the disbursement callback.
     *
     * @param  array{accountNumber: string, fullName: string, bankName: string, countryCode?: string, currency?: string}  $destination
     * @param  int|float|string  $amount  Whole amount in the transfer currency
     * @param  string  $externalReferenceId  Your unique reference: letters and digits only, at most 30 characters
     * @param  array{remarks?: string, type?: string, dateInEpoch?: int, additionalProperties?: array<string, mixed>, source?: array<string, string>}  $options
     * @return array<string, mixed> e.g. pgReferenceId, message, success, statusCode
     *
     * @throws ValidationException|ConfigurationException|RequestException
     */
    public function disburse(array $destination, int|float|string $amount, string $externalReferenceId, array $options = []): array
    {
        $source = array_merge((array) ($this->config['source'] ?? []), (array) ($options['source'] ?? []));

        foreach (['accountNumber', 'fullName', 'bankName'] as $field) {
            if (empty($source[$field])) {
                throw new ConfigurationException("The AzamPay disbursement source {$field} is not configured (azampay.disbursement.source.{$field}).");
            }

            if (empty($destination[$field])) {
                throw new ValidationException("The destination {$field} is required.");
            }
        }

        $source += ['countryCode' => 'TZ', 'currency' => 'TZS'];
        $destination += ['countryCode' => $source['countryCode'], 'currency' => $source['currency']];

        if (! is_numeric($amount) || (float) $amount <= 0 || (float) $amount != (int) $amount) {
            throw new ValidationException('The amount must be a whole number greater than zero.');
        }

        $amount = (int) $amount;

        if (preg_match('/^[A-Za-z0-9]{1,30}$/', $externalReferenceId) !== 1) {
            throw new ValidationException('The external reference must be 1 to 30 letters and digits, with no other characters.');
        }

        $dateInEpoch = (int) ($options['dateInEpoch'] ?? time());

        $checksum = Checksum::make(
            (string) ($this->config['public_key'] ?? ''),
            (string) $source['accountNumber'],
            (string) $destination['accountNumber'],
            (string) $source['currency'],
            $amount,
            $dateInEpoch,
            $externalReferenceId
        );

        $payload = [
            'source' => $this->party($source),
            'destination' => $this->party($destination),
            'transferDetails' => [
                'type' => (string) ($options['type'] ?? 'FUND'),
                'amount' => $amount,
                'dateInEpoch' => $dateInEpoch,
            ],
            'externalReferenceId' => $externalReferenceId,
            'checksum' => $checksum,
            'remarks' => (string) ($options['remarks'] ?? ''),
        ];

        if (! empty($options['additionalProperties'])) {
            $payload['additionalProperties'] = $options['additionalProperties'];
        }

        $response = $this->request()
            ->withHeaders(['X-Epoch-Date' => (string) $dateInEpoch])
            ->post($this->url('disburse'), $payload);

        return $this->decode($response);
    }

    /**
     * Get the current status of a transfer.
     *
     * @param  string  $pgReferenceId  The reference AzamPay returned from disburse()
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    public function transactionStatus(string $pgReferenceId, string $bankName): array
    {
        $response = $this->request()->get($this->url('transaction_status'), [
            'pgReferenceId' => $pgReferenceId,
            'bankName' => $bankName,
        ]);

        return $this->decode($response);
    }

    private function request(): PendingRequest
    {
        return Http::withToken($this->token)
            ->timeout($this->timeout)
            ->acceptJson()
            ->asJson();
    }

    /**
     * Build the URL for an endpoint. Each path can be set in
     * `azampay.disbursement.paths`; the defaults are AzamPay's.
     */
    private function url(string $endpoint): string
    {
        $configured = $this->config['paths'][$endpoint] ?? null;

        $path = is_string($configured) && $configured !== '' ? $configured : self::PATHS[$endpoint];

        return rtrim($this->baseUrl, '/').'/'.trim($path, '/');
    }

    /**
     * @param  array<string, mixed>  $party
     * @return array<string, string>
     */
    private function party(array $party): array
    {
        return [
            'countryCode' => (string) $party['countryCode'],
            'fullName' => (string) $party['fullName'],
            'bankName' => (string) $party['bankName'],
            'accountNumber' => (string) $party['accountNumber'],
            'currency' => (string) $party['currency'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(Response $response): array
    {
        $this->throwOnError($response);

        $decoded = $response->json();

        return is_array($decoded) ? $decoded : [];
    }

    private function throwOnError(Response $response): void
    {
        if ($response->status() === 401) {
            throw new AuthenticationException($response);
        }

        if ($response->failed()) {
            throw new RequestException($response);
        }
    }
}
