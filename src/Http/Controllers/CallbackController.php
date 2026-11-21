<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Http\Controllers;

use Alphaolomi\Azampay\Events\AzampayCallback;
use Alphaolomi\Azampay\Events\AzampayDisbursementCallback;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CallbackController
{
    /**
     * Checkout callback: the result of a mobile or bank checkout.
     */
    public function checkout(Request $request): Response
    {
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

        return response('', 200);
    }

    /**
     * Disbursement callback: the result of a transfer.
     */
    public function disbursement(Request $request): Response
    {
        $request->validate([
            'pgReferenceId' => ['required', 'string'],
            'status' => ['required', 'string'],
        ]);

        AzampayDisbursementCallback::dispatch($request->all());

        return response('', 200);
    }
}
