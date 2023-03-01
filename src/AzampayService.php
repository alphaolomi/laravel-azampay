<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay;

use Alphaolomi\Azampay\Concerns\BuildBaseRequest;
use Alphaolomi\Azampay\Concerns\CanSendGetRequest;
use Alphaolomi\Azampay\Concerns\CanSendPostRequest;
use Alphaolomi\Azampay\Traits\InputValidation;
use Exception;
use Illuminate\Http\Client\Response;
use \Illuminate\Http\Response as HTTPResponse;
use Illuminate\Support\Facades\Http;

class AzampayService
{
    use InputValidation;

    const SANDBOX_AUTH_BASE_URL = 'https://authenticator-sandbox.azampay.co.tz';

    const SANDBOX_BASE_URL = 'https://sandbox.azampay.co.tz';

    const AUTH_BASE_URL = 'https://authenticator.azampay.co.tz';

    const BASE_URL = 'https://checkout.azampay.co.tz';

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

    /**
     * @throws Exception
     */
    public function __construct() {

        if (empty(config('azampay.appName'))) {
            throw new \InvalidArgumentException("Missing required option: appName");
        }

        if (empty(config('azampay.clientId'))) {
            throw new \InvalidArgumentException("Missing required option: clientId");
        }

        if (empty(config('azampay.clientSecret'))) {
            throw new \InvalidArgumentException("Missing required option: clientSecret");
        }

        $this->baseUrl = config('azampay.environment') === 'sandbox' ? self::SANDBOX_BASE_URL : self::BASE_URL;
        $this->authBaseUrl = config('azampay.environment') === 'sandbox' ? self::SANDBOX_AUTH_BASE_URL : self::AUTH_BASE_URL;

        $this->generateToken();
    }

    /**
     * Generate Token
     *
     * Generate the access token in order to access Azampay public end points.
     * @throws Exception
     */
    public function generateToken(): array
    {
        $response = Http::post($this->authBaseUrl.'/AppRegistration/GenerateToken',
                    [
                        'appName' => config('azampay.appName'),
                        'clientId' => config('azampay.clientId'),
                        'clientSecret' => config('azampay.clientSecret')
                    ])->onError(function (Response $response) {
                             if ($response->status() === HTTPResponse::HTTP_LOCKED) {
                                 throw new Exception('Provided detail is not valid for this app or secret key has been expired');
                             }

                             if($response->serverError()) {
                                 throw new Exception('There is a problem with payment processing server.');
                             }
                        });

        $this->apiKey = $response->json('data')['accessToken']['type'];

        return $response->json();

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
    /**
     * @throws Exception
     */
    public function mobileCheckout(array $data)
    {
        $this->validateMNOCheckoutInput($data);

        $response = $this->sendRequest('post', '/azampay/mno/checkout', $data)
                    ->onError(function (Response $response) {
                        if ($response->badRequest()) {
                            throw new \RuntimeException($response->body());
                        }

                        if($response->serverError()) {
                            throw new Exception('There is a problem with payment processing server.');
                        }
                    });

        return $response->json();
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

        $this->validateBankCheckoutInput($data);

       $response = $this->sendRequest('post', '/azampay/bank/checkout', $data)
                    ->onError(function (Response $response) {
                         if ($response->badRequest()) {
                            throw new \RuntimeException($response->body());
                         }

                        if($response->serverError()) {
                            throw new Exception('There is a problem with payment processing server.');
                        }
                    });

       return $response->json();
    }

    public function getPaymentPartners(){
        $response = $this->sendRequest('get', '/api/v1/Partner/GetPaymentPartners', [])
            ->onError(function (Response $response) {
                if ($response->badRequest()) {
                    throw new \RuntimeException($response->body());
                }

                if($response->serverError()) {
                    throw new Exception('There is a problem with payment processing server.');
                }
            });

        return $response->json();
    }

    public function postCheckout(array $data)
    {
        $this->validatePostCheckoutInput($data);

        $response = $this->sendRequest('post', '/api/v1/Partner/PostCheckout', $data);

        return $response->json();
    }

    public function createTransfer(array $data)
    {
        $response = $this->sendDisbursementRequest('post', '/azampay/createtransfer', $data)
            ->onError(function (Response $response) {
                if ($response->unauthorized()) {
                    throw new \RuntimeException($response->body());
                }
            });

        return $response->json();
    }

    public function nameLookup(array $data)
    {
        $response = $this->sendDisbursementRequest('post', '/azampay/namelookup', $data)
            ->onError(function (Response $response) {
                if ($response->unauthorized()) {
                    throw new \RuntimeException($response->body());
                }
            });

        return $response->json();
    }


    public function getTransactionStatus(?array $data = null)
    {
        $response = $this->sendDisbursementRequest('get', '/azampay/gettransactionstatus', $data)
            ->onError(function (Response $response) {
                if ($response->unauthorized()) {
                    throw new \RuntimeException($response->body());
                }
            });

        return $response->json();
    }

    private function sendRequest(string $method, string $uri, array $data)
    {
        return Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'Content-Type' => 'application/json'
        ])->$method($this->baseUrl. $uri, $data);
    }

    private function sendDisbursementRequest(string $method, string $uri, ?array $data = null)
    {
        return Http::withHeaders([
                    'Authorization' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ])->$method($this->baseUrl. $uri, $data);
    }

}
