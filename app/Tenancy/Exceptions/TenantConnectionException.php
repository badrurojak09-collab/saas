<?php

namespace App\Tenancy\Exceptions;

use App\Exceptions\Tenancy\TenantConnectionException as BaseTenantConnectionException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class TenantConnectionException extends BaseTenantConnectionException implements HttpExceptionInterface
{
    private int $statusCode;

    private array $headers;

    public function __construct(
        string $message = 'Failed to establish tenant database connection.',
        int $statusCode = 503,
        ?Throwable $previous = null,
        array $headers = [],
        int $code = 0
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}
