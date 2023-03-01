<?php

namespace Alphaolomi\Azampay\Traits;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait InputValidation
{
    /**
     * @param  array<string, string>  $payload
     *
     * @throws Exception
     */
    private function validateGenerateInput(array $payload)
    {
        $validator = Validator::make($payload, [
            'appName' => ['required', 'string'],
            'clientId' => ['required', 'string'],
            'clientSecret' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $errors = json_encode($validator->errors()->all());
            throw new Exception($errors);
        }
    }

    private function validateMNOCheckoutInput(array $payload)
    {
        $validator = Validator::make($payload, [
            'amount' => ['required', 'numeric'],
            'currency' => ['required', 'string', Rule::in(['TZS'])],
            'accountNumber' => ['required', 'string'],
            'externalId' => ['required', 'string', 'max:128'],
            'provider' => ['required', 'string', Rule::in(['Airtel', 'Mpesa', 'Tigo', 'Halopesa', 'Azampesa'])],
        ]);

        if ($validator->fails()) {
            $errors = json_encode($validator->errors()->all());
            throw new Exception($errors);
        }
    }

    private function validateBankCheckoutInput(array $payload)
    {
        $validator = Validator::make($payload, [
            'merchantMobileNumber' => ['required', 'numeric'],
            'merchantName' => ['nullable', 'string'],
            'otp' => ['required', 'numeric'],
            'referenceId' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'currencyCode' => ['required', 'string', Rule::in(['TZS'])],
            'merchantAccountNumber' => ['required', 'string'],
            'provider' => ['required', 'string', Rule::in(['CRDB', 'NMB'])],
        ]);

        if ($validator->fails()) {
            $errors = json_encode($validator->errors()->all());
            throw new Exception($errors);
        }
    }

    private function validatePostCheckoutInput(array $payload)
    {
        $validator = Validator::make($payload, [
            'amount' => ['required', 'numeric'],
            'appName' => ['required', 'string'],
            'cart.items.*.name' => ['required', 'string'],
            'clientId' => ['required', 'string'],
            'currency' => ['required', 'string', Rule::in(['TZS'])],
            'externalId' => ['required', 'string', 'max:30'],
            'language' => ['required', 'string'],
            'redirectFailURL' => ['required', 'string'],
            'redirectSuccessURL' => ['required', 'string'],
            'requestOrigin' => ['required', 'string'],
            'vendorId' => ['required', 'string'],
            'vendorName' => ['required', 'string'],

        ]);

        if ($validator->fails()) {
            $errors = json_encode($validator->errors()->all());
            throw new Exception($errors);
        }
    }
}
