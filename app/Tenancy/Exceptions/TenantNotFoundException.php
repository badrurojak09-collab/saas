<?php

namespace App\Tenancy\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TenantNotFoundException extends HttpException
{
    public function __construct(string $message = 'Tenant not found.', ?\Throwable $previous = null)
    {
        parent::__construct(404, $message, $previous);
    }
}
