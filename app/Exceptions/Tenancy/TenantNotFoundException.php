<?php

namespace App\Exceptions\Tenancy;

use RuntimeException;

class TenantNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Tenant was not found.')
    {
        parent::__construct($message);
    }
}
