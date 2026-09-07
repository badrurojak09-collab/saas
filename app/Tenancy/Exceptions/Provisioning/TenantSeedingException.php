<?php

namespace App\Tenancy\Exceptions\Provisioning;

use App\Enums\Landlord\ProvisioningStep;
use Throwable;

class TenantSeedingException extends TenantProvisioningException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, ProvisioningStep::SEED_DATABASE, $previous);
    }
}
