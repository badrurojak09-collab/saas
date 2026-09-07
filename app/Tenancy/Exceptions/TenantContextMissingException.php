<?php

namespace App\Tenancy\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TenantContextMissingException extends HttpException
{
    public function __construct(string $message = 'Tenant context is missing. Querying tenant models requires an initialized tenant.', ?\Throwable $previous = null)
    {
        parent::__construct(500, $message, $previous);
    }
}
