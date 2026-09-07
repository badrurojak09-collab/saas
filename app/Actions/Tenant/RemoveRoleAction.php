<?php

namespace App\Actions\Tenant;

use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Contracts\TenantManager;

class RemoveRoleAction
{
    public function __construct(
        protected ?TenantManager $tenantManager = null,
    ) {}

    public function execute(User $user, array|string $roles): User
    {
        $tenantManager = $this->tenantManager ?? (app()->bound(TenantManager::class) ? app(TenantManager::class) : null);
        $tenantId = $tenantManager?->current()?->id;

        $previous = TenantPermissionContext::enter($tenantId);
        try {
            $user->removeRole($roles);
        } finally {
            TenantPermissionContext::leave($previous);
        }

        return $user->fresh();
    }
}
