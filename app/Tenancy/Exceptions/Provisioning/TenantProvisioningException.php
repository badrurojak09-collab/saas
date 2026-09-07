<?php

namespace App\Tenancy\Exceptions\Provisioning;

use App\Enums\Landlord\ProvisioningStep;
use RuntimeException;
use Throwable;

class TenantProvisioningException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?ProvisioningStep $step = null,
        ?Throwable $previous = null,
        int $code = 0
    ) {
        parent::__construct($message, $code, $previous);
    }
}
