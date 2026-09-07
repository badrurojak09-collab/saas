<?php

namespace App\Exceptions\Tenancy;

use RuntimeException;
use Throwable;

class TenantConnectionException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to configure tenant database connection.',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
