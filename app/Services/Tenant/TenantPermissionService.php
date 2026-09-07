<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\AssignRoleAction;
use App\Actions\Tenant\GrantPermissionAction;
use App\Actions\Tenant\RemoveRoleAction;
use App\Actions\Tenant\RevokePermissionAction;
use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Contracts\TenantManager;

class TenantPermissionService
{
    public function __construct(
        protected AssignRoleAction $assignRoleAction,
        protected RemoveRoleAction $removeRoleAction,
        protected GrantPermissionAction $grantPermissionAction,
        protected RevokePermissionAction $revokePermissionAction,
        protected TenantManager $tenantManager,
    ) {}

    public function assignRole(User $user, array|string $roles, ?string $performedBy = null): User
    {
        return $this->assignRoleAction->execute($user, $roles, $performedBy);
    }

    public function removeRole(User $user, array|string $roles): User
    {
        return $this->removeRoleAction->execute($user, $roles);
    }

    public function grantPermission(User|Role $target, array|string $permissions, ?string $performedBy = null): User|Role
    {
        return $this->grantPermissionAction->execute($target, $permissions, $performedBy);
    }

    public function revokePermission(User|Role $target, array|string $permissions, ?string $performedBy = null): User|Role
    {
        return $this->revokePermissionAction->execute($target, $permissions, $performedBy);
    }

    public function flushCache(?string $tenantId = null): void
    {
        $id = $tenantId ?? (string) $this->tenantManager->requireCurrent()->getKey();
        TenantPermissionContext::flushTenantCache($id);
    }
}
