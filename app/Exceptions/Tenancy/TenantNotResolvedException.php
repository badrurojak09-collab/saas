<?php

namespace App\Exceptions\Tenancy;

use RuntimeException;

class TenantNotResolvedException extends RuntimeException
{
    public function __construct(string $message = 'Tenant could not be resolved.')
    {
        parent::__construct($message);
    }
}
