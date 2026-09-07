<?php

namespace App\Tenancy\Contracts;

use App\Models\Landlord\Tenant;
use App\Tenancy\Exceptions\TenantInactiveException;
use App\Tenancy\Exceptions\TenantNotFoundException;

interface TenantResolver
{
    /**
     * Resolve a Tenant instance from an incoming HTTP host or domain string.
     *
     * @throws TenantNotFoundException
     * @throws TenantInactiveException
     */
    public function resolveFromHost(string $host): Tenant;

    /**
     * Resolve a Tenant instance from a Tenant ID or Code.
     *
     * @throws TenantNotFoundException
     * @throws TenantInactiveException
     */
    public function resolveFromId(string $tenantId): Tenant;
}
