<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay;

use Alphaolomi\Azampay\Traits\InputValidation;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Response as HTTPResponse;
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

    private string $baseUrl;

    private string $authBaseUrl;

    /**
     * @deprecated use $token instead
     *             This will be removed in the next major release,
     *             use $token instead of $apiKey, if you are using
     *             this property in your code.
     */

    // @phpstan-ignore-next-line
    private string $apiKey;

    private string $token;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (empty(config('azampay.appName'))) {
            throw new \InvalidArgumentException('Missing required option: appName');
        }

        if (empty(config('azampay.clientId'))) {
            throw new \InvalidArgumentException('Missing required option: clientId');
        }

        if (empty(config('azampay.clientSecret'))) {
            throw new \InvalidArgumentException('Missing required option: clientSecret');
        }

        $this->baseUrl = config('azampay.environment') === 'sandbox' ? self::SANDBOX_BASE_URL : self::BASE_URL;
        $this->authBaseUrl = config('azampay.environment') === 'sandbox' ? self::SANDBOX_AUTH_BASE_URL : self::AUTH_BASE_URL;

        $this->generateToken();
    }

    /**
     * Generate Token
     *
     * Generate the access token in order to access Azampay public end points.
     *
     * @throws Exception
     */
    public function generateToken(): void
    {
        $response = Http::post(
            $this->authBaseUrl.'/AppRegistration/GenerateToken',
            [
                'appName' => config('azampay.appName'),
                'clientId' => config('azampay.clientId'),
                'clientSecret' => config('azampay.clientSecret'),
            ]
        )->onError(function (Response $response) {
            if ($response->status() === HTTPResponse::HTTP_LOCKED) {
                throw new Exception('Provided detail is not valid for this app or secret key has been expired');
            }

            if ($response->serverError()) {
                throw new Exception('There is a problem with payment processing server.');
            }
        });

        $this->token = $response->json('data')['accessToken'];
    }

    /**
     * Perform mobile checkout via Azampay
     *
     * @param  array  $data  {
     *                       'accountNumber': string,
     *                       'additionalProperties'?: array{
     *                       'property1': null,
     *                       'property2': null
     *                       },
     *                       'amount': string,
     *                       'currency': string,
     *                       'externalId': string,
     *                       'provider': Airtel | Tigo | Mpesa | Azampesa | Halopesa
     *                       }
     * @return array|null{
     *   'transactionId': string,
     *   'message': string
     * }|null
     *
     * @throws Exception
     */
    public function mobileCheckout(array $data): ?array
    {
        $this->validateMNOCheckoutInput($data);

        $response = $this->sendRequest('post', '/azampay/mno/checkout', $data)
            ->onError(function (Response $response) {
                if ($response->badRequest()) {
                    throw new \RuntimeException($response->body());
                }

                if ($response->serverError()) {
                    throw new Exception('There is a problem with payment processing server.');
                }
            });

        return $response->json();
    }

    /**
     * Perform bank checkout via Azampay
     *
     * @param  $data  array {
     *
     *   additionalProperties: array {
     *   property1: null,
     *   property2: null
     *   },
     *   amount: string,
     *   currencyCode: string,
     *   merchantAccountNumber: string,
     *   merchantMobileNumber: string,
     *   merchantName: string,
     *   otp: string,
     *   provider: CRDB | NMB,
     *   referenceId: string
     * }
     * @return array|null {
     *                    transactionId: string,
     *                    message: string
     *                    }|null
     *
     * @throws Exception
     */
    public function bankCheckout(array $data): ?array
    {
        $this->validateBankCheckoutInput($data);

        $response = $this->sendRequest('post', '/azampay/bank/checkout', $data)
            ->onError(function (Response $response) {
                if ($response->badRequest()) {
                    throw new \RuntimeException($response->body());
                }

                if ($response->serverError()) {
                    throw new Exception('There is a problem with payment processing server.');
                }
            });

        return $response->json();
    }

    /**
     * Get payment partners via Azampay
     *
     * @return array|null {
     *                    [
     *                    {
     *                    logoUrl: string,
     *                    partnerName: string,
     *                    provider: 0,
     *                    vendorName: string",
     *                    paymentVendorId: string,
     *                    paymentPartnerId: string,
     *                    currency: string
     *                    }
     *                    ]
     *                    }|null
     *
     * @throws Exception
     */
    public function getPaymentPartners(): ?array
    {
        $response = $this->sendRequest('get', '/api/v1/Partner/GetPaymentPartners', [])
            ->onError(function (Response $response) {
                if ($response->badRequest()) {
                    throw new \RuntimeException($response->body());
                }

                if ($response->serverError()) {
                    throw new Exception('There is a problem with payment processing server.');
                }
            });

        return $response->json();
    }

    /**
     * Perform post checkout via Azampay
     *
     * @param  $data  array {
     *               appName: string,
     *               clientId: string,
     *               vendorId: e9b57fab-1850-44d4-8499-71fd15c845a0,
     *               language: string,
     *               currency: string,
     *               externalId: string,
     *               requestOrigin: string,
     *               redirectFailURL: string,
     *               redirectSuccessURL: string,
     *               vendorName: string,
     *               amount: string",
     *               cart: {
     *               items: [
     *               {
     *               name: string
     *               }
     *               ]
     *               }
     *
     * }
     *
     * @throws Exception
     */
    public function postCheckout(array $data): string
    {
        $this->validatePostCheckoutInput($data);

        $response = $this->sendRequest('post', '/api/v1/Partner/PostCheckout', $data);

        return $response->body();
    }

    /**
     * Perform bank checkout via Azampay
     *
     * @param  $data  array {
     *
     *   source : {
     *      countryCode: string,
     *      fullName: string,
     *      bankName: string,
     *      accountNumber: string,
     *      currency: string
     *   },
     *   destination: {
     *      countryCode: string,
     *      fullName: string,
     *      bankName: string,
     *      accountNumber: string,
     *      currency: string
     *   },
     *   transferDetails: {
     *      type: string,
     *      amount: 0,
     *      date: string
     *   },
     *   externalReferenceId: string,
     *   remarks: string
     * }
     * @return array|null {
     *                    data: Transaction successful.,
     *                    message: Request successful.,
     *                    success: true,
     *                    statusCode: 200
     *                    }|null
     *
     * @throws Exception
     */
    public function createTransfer(array $data): ?array
    {
        $response = $this->sendDisbursementRequest('post', '/azampay/createtransfer', $data)
            ->onError(function (Response $response) {
                if ($response->unauthorized()) {
                    throw new \RuntimeException($response->body());
                }
            });

        return $response->json();
    }

    /**
     * Perform name lookup via Azampay
     *
     * @param  $data  array {
     *               bankName: string,
     *               accountNumber: string
     *               }
     * @return array|null {
     *                    name: string,
     *                    message: string,
     *                    success: true,
     *                    accountNumber: string,
     *                    bankName: string
     *                    }|null
     *
     * @throws Exception
     */
    public function nameLookup(array $data): ?array
    {
        $response = $this->sendDisbursementRequest('post', '/azampay/namelookup', $data)
            ->onError(function (Response $response) {
                if ($response->unauthorized()) {
                    throw new \RuntimeException($response->body());
                }
            });

        return $response->json();
    }

    /**
     * Perform bank checkout via Azampay
     *
     * @param  $data  array | null {
     *               bankName: CRDB | NMB, pgReferenceId: string
     *               }
     * @return array|null {
     *                    data: Transaction successful.,
     *                    message: Request successful.,
     *                    success: true,
     *                    statusCode": 200
     *                    }|null
     *
     * @throws Exception
     */
    public function getTransactionStatus(?array $data = null): ?array
    {
        $response = $this->sendDisbursementRequest('get', '/azampay/gettransactionstatus', $data)
            ->onError(function (Response $response) {
                if ($response->unauthorized()) {
                    throw new \RuntimeException($response->body());
                }
            });

        return $response->json();
    }

    /**
     * Prepare request to be sent to Azampay
     */
    private function sendRequest(string $method, string $uri, array $data): Response
    {
        return Http::withToken($this->token)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->$method($this->baseUrl.$uri, $data);
    }

    /**
     * Prepare disbursement request to be sent to Azampay
     */
    private function sendDisbursementRequest(string $method, string $uri, ?array $data = null): Response
    {
        return Http::withToken($this->token)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->$method($this->baseUrl.$uri, $data);
    }
}
