<?php

namespace App\Tenancy\Contracts;

use App\Models\Landlord\Tenant;
use App\Tenancy\Exceptions\TenantConnectionException;
use App\Tenancy\Exceptions\TenantContextMissingException;
use App\Tenancy\Exceptions\TenantDatabaseUnavailableException;

interface TenantManager
{
    /**
     * Initialize the tenancy environment for the given Tenant:
     * loads database metadata, configures dynamic connection, and activates context.
     *
     * @throws TenantConnectionException
     * @throws TenantDatabaseUnavailableException
     */
    public function initialize(Tenant $tenant): void;

    /**
     * Retrieve the currently active Tenant, or null if no tenant is initialized.
     */
    public function current(): ?Tenant;

    /**
     * Retrieve the currently active Tenant, or throw an exception if missing.
     *
     * @throws TenantContextMissingException
     */
    public function requireCurrent(): Tenant;

    /**
     * Terminate the tenant execution context and disconnect/purge the tenant database connection.
     */
    public function end(): void;

    /**
     * Determine whether a tenant context has been initialized.
     */
    public function isInitialized(): bool;
}
