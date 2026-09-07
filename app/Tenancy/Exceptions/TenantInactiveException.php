<?php

namespace App\Tenancy\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TenantInactiveException extends HttpException
{
    public function __construct(string $message = 'Tenant is not active.', int $statusCode = 403, ?\Throwable $previous = null)
    {
        parent::__construct($statusCode, $message, $previous);
    }
}
