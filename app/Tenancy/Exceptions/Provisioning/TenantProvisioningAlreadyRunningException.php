<?php

namespace App\Tenancy\Exceptions\Provisioning;

use Throwable;

class TenantProvisioningAlreadyRunningException extends TenantProvisioningException
{
    public function __construct(string $message = 'Tenant provisioning is already running.', ?Throwable $previous = null)
    {
        parent::__construct($message, null, $previous);
    }
}
