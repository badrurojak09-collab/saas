<?php

namespace App\Support\Tenancy;

use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use Spatie\Permission\PermissionRegistrar;

final class TenantPermissionContext
{
    /** @return array{role: string, permission: string, cacheKey: string} */
    public static function enter(?string $tenantId = null): array
    {
        $registrar = app(PermissionRegistrar::class);
        $previous = [
            'role' => $registrar->getRoleClass(),
            'permission' => $registrar->getPermissionClass(),
            'cacheKey' => $registrar->cacheKey,
        ];

        $registrar->setRoleClass(Role::class);
        $registrar->setPermissionClass(Permission::class);

        if ($tenantId !== null) {
            $registrar->cacheKey = 'tenant:'.$tenantId.':permission-cache';
        }

        $registrar->forgetCachedPermissions();

        return $previous;
    }

    /** @param array{role: string, permission: string, cacheKey?: string} $previous */
    public static function leave(array $previous): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->setRoleClass($previous['role']);
        $registrar->setPermissionClass($previous['permission']);

        if (isset($previous['cacheKey'])) {
            $registrar->cacheKey = $previous['cacheKey'];
        }

        $registrar->forgetCachedPermissions();
    }

    public static function flushTenantCache(string $tenantId): void
    {
        $registrar = app(PermissionRegistrar::class);
        $originalKey = $registrar->cacheKey;
        $registrar->cacheKey = 'tenant:'.$tenantId.':permission-cache';
        $registrar->forgetCachedPermissions();
        $registrar->cacheKey = $originalKey;
    }
}
