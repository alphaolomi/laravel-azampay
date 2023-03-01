<?php

declare(strict_types=1);

namespace AlphaOlomi\Azampay\Concerns;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait BuildBaseRequest
{
    public function buildRequestWithToken(): PendingRequest
    {
        return $this
            ->withBaseUrl()
            ->timeout(15)
            ->asJson()
            ->acceptJson()
            // ->withToken($this->options['token']);
            ->withHeaders(headers: ['X-API-Key' => "{$this->apiKey}"]); // testing only
    }

    public function buildRequest(): PendingRequest
    {
        return $this
            ->withBaseUrl()
            ->timeout(15)
            ->asJson()
            ->acceptJson();
    }

    public function buildBareRequest(): PendingRequest
    {
        return  $this
            ->timeout(15)
            ->asJson()
            ->acceptJson();
    }

    public function withBaseUrl(): PendingRequest
    {
        return Http::baseUrl(url: $this->baseUrl);
    }
}
