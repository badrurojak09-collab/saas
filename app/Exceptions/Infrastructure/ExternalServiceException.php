<?php

namespace App\Exceptions\Infrastructure;

use RuntimeException;
use Throwable;

class ExternalServiceException extends RuntimeException
{
    public function __construct(
        string $message = 'External service call failed.',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
