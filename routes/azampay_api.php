<?php

use Alphaolomi\Azampay\Events\AzampayCallback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/api/v1/Checkout/Callback', function (Request $request) {
    $request->validate([
        'amount' => ['required', 'string'],
        'message' => ['required', 'string'],
        'msisdn' => ['required', 'string'],
        'operator' => ['required', 'string'],
        'reference' => ['required', 'string'],
        'submerchantAcc' => ['nullable', 'string'],
        'transactionstatus' => ['required', 'string'],
        'utilityref' => ['required', 'string'],
    ]);

    AzampayCallback::dispatch($request->all());
})->name('checkout_payment_callback');
