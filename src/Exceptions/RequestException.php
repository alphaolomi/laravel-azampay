<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Exceptions;

use Illuminate\Http\Client\Response;

/**
 * AzamPay answered with an error status.
 */
class RequestException extends AzampayException
{
    public function __construct(public readonly Response $response, ?string $message = null)
    {
        parent::__construct(
            $message ?? sprintf('AzamPay responded with HTTP %d: %s', $response->status(), $response->body()),
            $response->status()
        );
    }

    public function status(): int
    {
        return $this->response->status();
    }

    /**
     * @return array<string, mixed>
     */
    public function json(): array
    {
        $decoded = $this->response->json();

        return is_array($decoded) ? $decoded : [];
    }
}
