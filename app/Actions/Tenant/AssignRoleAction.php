<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\AssignRoleData;
use App\Events\Tenant\TenantRoleAssigned;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Contracts\TenantManager;

class AssignRoleAction
{
    public function __construct(
        protected ?TenantManager $tenantManager = null,
    ) {}

    public function execute(User|AssignRoleData $data, array|string|null $roles = null, ?string $performedBy = null): User
    {
        if ($data instanceof AssignRoleData) {
            $user = User::findOrFail($data->userId);
            $roles = $data->roles;
            $performedBy = $data->performedBy;
        } else {
            $user = $data;
        }

        $tenantManager = $this->tenantManager ?? (app()->bound(TenantManager::class) ? app(TenantManager::class) : null);
        $tenantId = $tenantManager?->current()?->id;

        $previous = TenantPermissionContext::enter($tenantId);
        try {
            $user->assignRole($roles);
        } finally {
            TenantPermissionContext::leave($previous);
        }

        if ($tenantId) {
            event(new TenantRoleAssigned((string) $tenantId, (string) $user->id, $roles, $performedBy));
        }

        return $user->fresh();
    }
}
