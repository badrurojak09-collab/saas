<?php

namespace App\Exceptions\Infrastructure;

use RuntimeException;
use Throwable;

class DatabaseConnectionException extends RuntimeException
{
    public function __construct(
        string $message = 'Database connection failed.',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
