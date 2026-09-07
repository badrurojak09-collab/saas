<?php

namespace Tests\Unit;

use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use Tests\TestCase;

final class TenantIdentityTest extends TestCase
{
    public function test_tenant_user_and_permission_models_use_tenant_connection(): void
    {
        $this->assertSame('tenant', (new User)->getConnectionName());
        $this->assertSame('tenant', (new Role)->getConnectionName());
        $this->assertSame('tenant', (new Permission)->getConnectionName());
        $reflection = new \ReflectionClass(User::class);
        $guard = $reflection->getDefaultProperties()['guard_name'] ?? null;
        $this->assertSame('tenant', $guard);
    }

    public function test_permission_context_restores_original_models(): void
    {
        $originalRole = config('permission.models.role');
        $originalPermission = config('permission.models.permission');

        $previous = TenantPermissionContext::enter();
        try {
            $this->assertSame(Role::class, config('permission.models.role'));
            $this->assertSame(Permission::class, config('permission.models.permission'));
        } finally {
            TenantPermissionContext::leave($previous);
        }

        $this->assertSame($originalRole, config('permission.models.role'));
        $this->assertSame($originalPermission, config('permission.models.permission'));
    }
}
