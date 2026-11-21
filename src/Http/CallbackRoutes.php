<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Http;

use Alphaolomi\Azampay\Http\Controllers\CallbackController;
use Alphaolomi\Azampay\Http\Middleware\VerifyAzampayCallback;
use Illuminate\Support\Facades\Route;

/**
 * Registers the callback routes from the `azampay.callback` configuration.
 */
class CallbackRoutes
{
    public const CHECKOUT_PATH = '/api/v1/Checkout/Callback';

    public const DISBURSEMENT_PATH = '/api/v1/Disbursement/Callback';

    public static function register(): void
    {
        $config = (array) config('azampay.callback', []);
        $middleware = array_values((array) ($config['middleware'] ?? []));

        if ($config['enabled'] ?? true) {
            Route::post(self::path($config['path'] ?? null, self::CHECKOUT_PATH), [CallbackController::class, 'checkout'])
                ->middleware(array_merge([VerifyAzampayCallback::class.':checkout'], $middleware))
                ->name('checkout_payment_callback');
        }

        if ($config['disbursement_enabled'] ?? false) {
            Route::post(self::path($config['disbursement_path'] ?? null, self::DISBURSEMENT_PATH), [CallbackController::class, 'disbursement'])
                ->middleware(array_merge([VerifyAzampayCallback::class.':disbursement'], $middleware))
                ->name('azampay_disbursement_callback');
        }
    }

    private static function path(mixed $configured, string $default): string
    {
        return is_string($configured) && $configured !== '' ? $configured : $default;
    }
}
