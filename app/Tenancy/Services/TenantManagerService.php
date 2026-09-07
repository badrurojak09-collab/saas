<?php

namespace App\Tenancy\Services;

use App\Enums\Landlord\TenantStatus;
use App\Models\Landlord\Tenant;
use App\Services\Tenant\TenantDatabaseService;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Contracts\TenantConnectionManager as TenantConnectionManagerContract;
use App\Tenancy\Contracts\TenantContext as TenantContextContract;
use App\Tenancy\Contracts\TenantManager as TenantManagerContract;
use App\Tenancy\Exceptions\TenantConnectionException;
use App\Tenancy\Exceptions\TenantInactiveException;

class TenantManagerService implements TenantManagerContract
{
    private mixed $permissionContext = null;

    public function __construct(
        private readonly TenantContextContract $context,
        private readonly TenantConnectionManagerContract $connectionManager,
        private readonly TenantDatabaseService $databaseService,
    ) {}

    public function initialize(Tenant $tenant): void
    {
        if ($this->isInitialized()) {
            $current = $this->current();
            if ($current && (string) $current->getKey() === (string) $tenant->getKey()) {
                // Idempotent call for the same tenant
                return;
            }

            throw new TenantConnectionException(
                "Tenant context is already initialized with [{$current?->code}]. You must call end() before switching tenants."
            );
        }

        if ($tenant->status !== TenantStatus::ACTIVE) {
            $statusValue = $tenant->status instanceof TenantStatus
                ? $tenant->status->value
                : (string) $tenant->status;

            throw new TenantInactiveException(
                "Cannot initialize inactive tenant [{$tenant->code}] [status: {$statusValue}]."
            );
        }

        $database = $this->databaseService->loadMetadata($tenant);

        // Configure connection parameters and purge old connection
        $this->connectionManager->configure($database);

        // Reconnect dynamically to the tenant database
        $this->connectionManager->connect();

        // Register tenant context
        $this->context->set($tenant);

        // Enter tenant permission context for Spatie permissions
        $this->permissionContext = TenantPermissionContext::enter((string) $tenant->getKey());
    }

    public function current(): ?Tenant
    {
        return $this->context->get();
    }

    public function requireCurrent(): Tenant
    {
        return $this->context->require();
    }

    public function end(): void
    {
        if ($this->permissionContext !== null) {
            TenantPermissionContext::leave($this->permissionContext);
            $this->permissionContext = null;
        }

        $this->connectionManager->disconnect();
        $this->context->clear();
    }

    public function isInitialized(): bool
    {
        return $this->context->has();
    }

    public function getConnectionManager(): TenantConnectionManagerContract
    {
        return $this->connectionManager;
    }
}
