<?php

namespace App\Actions\Tenant;

use App\Events\Tenant\TenantPermissionChanged;
use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Contracts\TenantManager;

class GrantPermissionAction
{
    public function __construct(
        protected ?TenantManager $tenantManager = null,
    ) {}

    public function execute(User|Role $target, array|string $permissions, ?string $performedBy = null): User|Role
    {
        $tenantManager = $this->tenantManager ?? (app()->bound(TenantManager::class) ? app(TenantManager::class) : null);
        $tenantId = $tenantManager?->current()?->id;

        $previous = TenantPermissionContext::enter($tenantId);
        try {
            $target->givePermissionTo($permissions);
        } finally {
            TenantPermissionContext::leave($previous);
        }

        if ($tenantId) {
            event(new TenantPermissionChanged(
                (string) $tenantId,
                $target::class,
                (string) $target->id,
                'granted',
                $permissions,
                $performedBy
            ));
        }

        return $target->fresh();
    }
}
