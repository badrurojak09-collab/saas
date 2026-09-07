<?php

namespace App\Tenancy\Exceptions\Provisioning;

use App\Enums\Landlord\ProvisioningStep;
use Throwable;

class TenantMigrationException extends TenantProvisioningException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, ProvisioningStep::MIGRATE_DATABASE, $previous);
    }
}
