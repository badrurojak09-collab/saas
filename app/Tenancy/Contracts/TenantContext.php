<?php

namespace App\Tenancy\Contracts;

use App\Models\Landlord\Tenant;
use App\Tenancy\Exceptions\TenantContextMissingException;

interface TenantContext
{
    /**
     * Store the active Tenant in the request-scoped context.
     */
    public function set(Tenant $tenant): void;

    /**
     * Get the active Tenant, or null if none is set.
     */
    public function get(): ?Tenant;

    /**
     * Get the active Tenant or throw an exception if none is set.
     *
     * @throws TenantContextMissingException
     */
    public function require(): Tenant;

    /**
     * Clear the tenant context.
     */
    public function clear(): void;

    /**
     * Check if a tenant is currently set in the context.
     */
    public function has(): bool;
}
