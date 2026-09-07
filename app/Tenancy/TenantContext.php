<?php

namespace App\Tenancy;

use App\Contracts\Tenancy\IdentifiesTenant;
use App\Exceptions\Tenancy\TenantNotResolvedException;

final class TenantContext
{
    private ?IdentifiesTenant $tenant = null;

    public function set(IdentifiesTenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function current(): IdentifiesTenant
    {
        if (! $this->has()) {
            throw new TenantNotResolvedException;
        }

        return $this->tenant;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }
}
