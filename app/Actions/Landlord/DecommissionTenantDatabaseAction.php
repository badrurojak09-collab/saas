<?php

namespace App\Actions\Landlord;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Models\Landlord\PlatformAuditLog;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;

class DecommissionTenantDatabaseAction
{
    public function __construct(
        protected TenantConnectionManager $connectionManager,
        protected TenantContext $tenantContext,
    ) {}

    /**
     * Decommission a tenant database and update tenant state.
     */
    public function execute(TenantDatabase $database, ?string $actorId = null): void
    {
        $oldStatus = $database->status?->value ?? (string) $database->status;
        $tenant = $database->tenant;

        $database->update([
            'status' => TenantDatabaseStatus::DECOMMISSIONED,
        ]);

        if ($tenant) {
            $tenant->update([
                'status' => TenantStatus::ARCHIVED,
            ]);
        }

        $this->connectionManager->disconnect();
        $this->tenantContext->clear();

        PlatformAuditLog::query()->create([
            'tenant_id' => $tenant?->getKey(),
            'actor_id' => $actorId,
            'action' => 'decommission_database',
            'entity_type' => TenantDatabase::class,
            'entity_id' => (string) $database->getKey(),
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => TenantDatabaseStatus::DECOMMISSIONED->value],
            'created_at' => now(),
        ]);
    }
}
