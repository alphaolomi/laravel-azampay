<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay;

use AlphaOlomi\Azampay\Concerns\BuildBaseRequest;
use AlphaOlomi\Azampay\Concerns\CanSendGetRequest;
use AlphaOlomi\Azampay\Concerns\CanSendPostRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AzampayService
{
    use BuildBaseRequest;
    use CanSendGetRequest;
    use CanSendPostRequest;

    const SANDBOX_AUTH_BASE_URL = 'https://authenticator-sandbox.azampay.co.tz';

    const SANDBOX_BASE_URL = 'https://sandbox.azampay.co.tz';

    const AUTH_BASE_URL = '';

    const BASE_URL = '';

    const SUPPORTED_MNO = ['Airtel', 'Tigo', 'Halopesa', 'Azampesa'];

    const SUPPORTED_BANK = ['CRDB', 'NMB'];

    const SUPPORTED_CURRENCY = ['TZS'];

    // appName
    // clientId
    // clientSecret
    // environment

    private $baseUrl;

    private $authBaseUrl;

    private $apiKey;

    public function __construct(
        private array $options = []
    ) {
        foreach (['appName', 'clientId', 'clientSecret'] as $key) {
            if (! isset($this->options[$key]) || empty($this->options[$key])) {
                throw new \InvalidArgumentException("Missing required option: $key");
            }
        }

        $this->options['environment'] = $this->options['environment'] ?? 'sandbox';

        $this->baseUrl = $this->options['environment'] === 'sandbox' ? self::SANDBOX_BASE_URL : self::BASE_URL;
        $this->authBaseUrl = $this->options['environment'] === 'sandbox' ? self::SANDBOX_AUTH_BASE_URL : self::AUTH_BASE_URL;
    }

    /**
     * Generate Token
     *
     * Generate the access token in order to access Azampay public end points.
     *
     * @return array
     */
    public function generateToken(): array
    {
        return $this->get(
            request: Http::baseUrl(url: $this->authBaseUrl), // FIXME: bad code
            url: '/AppRegistration/GenerateToken',
        )->onError(function (Response $response) {
            if ($response->status() === 423) {
                throw new \Exception('Provided detail is not valid for this app or secret key has been expired');
            }
        })->json('data');
        // accessToken
        // expire
    }

    // "accountNumber": "string",
    // "additionalProperties": {
    // "property1": null,
    // "property2": null
    // },
    // "amount": "string",
    // "currency": "string",
    // "externalId": "string",
    // "provider": "Airtel"
    public function mobileCheckout(array $data)
    {
        // azampay/mno/checkout
        return $this->post(
            request: $this->service->buildRequestWithToken(),
            url: '/azampay/mno/checkout',
            payload: $data
        )->onError(function (Response $response) {
            // if ($response->status() === 400) {
            throw new \RuntimeException($response->body());
            // }
        })->json();
    }

    // "additionalProperties": {
    //     "property1": null,
    //     "property2": null
    //     },
    //     "amount": "string",
    //     "currencyCode": "string",
    //     "merchantAccountNumber": "string",
    //     "merchantMobileNumber": "string",
    //     "merchantName": "string",
    //     "otp": "string",
    //     "provider": "CRDB",
    //     "referenceId": "string"
    public function bankCheckout(array $data)
    {
        // azampay/bank/checkout
        return $this->post(
            request: $this->service->buildRequestWithToken(),
            url: '/azampay/mno/checkout',
            payload: $data
        )->onError(function (Response $response) {
            // if ($response->status() === 400) {
            throw new \RuntimeException($response->body());
            // }
        })->json();
    }
}
