<?php

namespace App\Tenancy\Exceptions;

use RuntimeException;

class TenantDatabaseNotConfiguredException extends RuntimeException
{
    public function __construct(string $message = 'Tenant database has not been configured or is missing.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
