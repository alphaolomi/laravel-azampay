<?php

namespace Alphaolomi\Azampay\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * AzamPay reported the result of a transfer.
 *
 * `$data` holds the callback fields, including `pgReferenceId` and `status`
 * ("success" or "failure"). Confirm the result with
 * `Azampay::disbursement()->transactionStatus()` before you act on it.
 */
class AzampayDisbursementCallback
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(public array $data) {}
}
