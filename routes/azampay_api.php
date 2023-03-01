<?php

use Alphaolomi\Azampay\Events\AzampayCallback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::post('/api/v1/Checkout/Callback', function (Request $request) {
    $request->validate([
        'amount' => ['required', 'string'],
        'message' => ['required', 'string'],
        'msisdn' => ['required', 'string'],
        'operator' => ['required', 'string', Rule::in(['Airtel', 'Mpesa', 'Tigo', 'Halopesa', 'Azampesa'])],
        'reference' => ['required', 'string'],
        'submerchantAcc' => ['required', 'string'],
        'transactionstatus' => ['required', 'string'],
        'utilityref' => ['required', 'string'],
    ]);

    AzampayCallback::dispatch($request->all());
})->name('checkout_payment_callback');
