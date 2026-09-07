<?php

namespace App\Tenancy\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TenantDatabaseUnavailableException extends HttpException
{
    public function __construct(string $message = 'Tenant database is currently unavailable.', ?\Throwable $previous = null)
    {
        parent::__construct(503, $message, $previous);
    }
}
