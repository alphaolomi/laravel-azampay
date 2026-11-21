<?php

declare(strict_types=1);

namespace Alphaolomi\Azampay\Exceptions;

/**
 * AzamPay rejected the access token (HTTP 401).
 */
class AuthenticationException extends RequestException {}
