<?php

namespace App\Tenancy\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TenantDomainNotFoundException extends HttpException
{
    public function __construct(string $message = 'Tenant domain is not registered, verified, or active.', ?\Throwable $previous = null)
    {
        parent::__construct(404, $message, $previous);
    }
}
