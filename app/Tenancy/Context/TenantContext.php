<?php

namespace App\Tenancy\Context;

use App\Models\Landlord\Tenant;
use App\Tenancy\Contracts\TenantContext as TenantContextContract;
use App\Tenancy\Exceptions\TenantContextMissingException;

class TenantContext implements TenantContextContract
{
    private ?Tenant $tenant = null;

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    public function require(): Tenant
    {
        if (! $this->has()) {
            throw new TenantContextMissingException;
        }

        return $this->tenant;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }
}
