<?php

namespace Tests\Unit\Tenant\Services;

use App\Actions\Tenant\AssignRoleAction;
use App\Actions\Tenant\GrantPermissionAction;
use App\Actions\Tenant\RemoveRoleAction;
use App\Actions\Tenant\RevokePermissionAction;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use App\Services\Tenant\TenantPermissionService;
use App\Tenancy\Contracts\TenantManager;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

final class TenantPermissionServiceTest extends TestCase
{
    public function test_assign_role_delegates_to_assign_role_action(): void
    {
        $assignAction = $this->createMock(AssignRoleAction::class);
        $removeAction = $this->createMock(RemoveRoleAction::class);
        $grantAction = $this->createMock(GrantPermissionAction::class);
        $revokeAction = $this->createMock(RevokePermissionAction::class);
        $manager = $this->createMock(TenantManager::class);

        $user = new User;
        $user->id = 'user-1';

        $assignAction->expects($this->once())
            ->method('execute')
            ->with($user, ['admin'], 'performed-by-1')
            ->willReturn($user);

        $service = new TenantPermissionService($assignAction, $removeAction, $grantAction, $revokeAction, $manager);

        $result = $service->assignRole($user, ['admin'], 'performed-by-1');
        $this->assertSame($user, $result);
    }

    public function test_remove_role_delegates_to_remove_role_action(): void
    {
        $assignAction = $this->createMock(AssignRoleAction::class);
        $removeAction = $this->createMock(RemoveRoleAction::class);
        $grantAction = $this->createMock(GrantPermissionAction::class);
        $revokeAction = $this->createMock(RevokePermissionAction::class);
        $manager = $this->createMock(TenantManager::class);

        $user = new User;
        $user->id = 'user-1';

        $removeAction->expects($this->once())
            ->method('execute')
            ->with($user, ['staff'])
            ->willReturn($user);

        $service = new TenantPermissionService($assignAction, $removeAction, $grantAction, $revokeAction, $manager);

        $result = $service->removeRole($user, ['staff']);
        $this->assertSame($user, $result);
    }

    public function test_grant_permission_delegates_to_grant_permission_action(): void
    {
        $assignAction = $this->createMock(AssignRoleAction::class);
        $removeAction = $this->createMock(RemoveRoleAction::class);
        $grantAction = $this->createMock(GrantPermissionAction::class);
        $revokeAction = $this->createMock(RevokePermissionAction::class);
        $manager = $this->createMock(TenantManager::class);

        $role = new Role;
        $role->id = 'role-1';

        $grantAction->expects($this->once())
            ->method('execute')
            ->with($role, ['users.view'], 'performed-by-1')
            ->willReturn($role);

        $service = new TenantPermissionService($assignAction, $removeAction, $grantAction, $revokeAction, $manager);

        $result = $service->grantPermission($role, ['users.view'], 'performed-by-1');
        $this->assertSame($role, $result);
    }

    public function test_revoke_permission_delegates_to_revoke_permission_action(): void
    {
        $assignAction = $this->createMock(AssignRoleAction::class);
        $removeAction = $this->createMock(RemoveRoleAction::class);
        $grantAction = $this->createMock(GrantPermissionAction::class);
        $revokeAction = $this->createMock(RevokePermissionAction::class);
        $manager = $this->createMock(TenantManager::class);

        $role = new Role;
        $role->id = 'role-1';

        $revokeAction->expects($this->once())
            ->method('execute')
            ->with($role, ['users.delete'], 'performed-by-1')
            ->willReturn($role);

        $service = new TenantPermissionService($assignAction, $removeAction, $grantAction, $revokeAction, $manager);

        $result = $service->revokePermission($role, ['users.delete'], 'performed-by-1');
        $this->assertSame($role, $result);
    }

    public function test_flush_cache_clears_tenant_permission_cache(): void
    {
        $assignAction = $this->createMock(AssignRoleAction::class);
        $removeAction = $this->createMock(RemoveRoleAction::class);
        $grantAction = $this->createMock(GrantPermissionAction::class);
        $revokeAction = $this->createMock(RevokePermissionAction::class);
        $manager = $this->createMock(TenantManager::class);

        $tenant = new Tenant;
        $tenant->id = 'tenant-uuid-123';

        $manager->expects($this->once())
            ->method('requireCurrent')
            ->willReturn($tenant);

        $service = new TenantPermissionService($assignAction, $removeAction, $grantAction, $revokeAction, $manager);

        $registrar = app(PermissionRegistrar::class);
        $initialKey = $registrar->cacheKey;

        $service->flushCache();

        $this->assertSame($initialKey, $registrar->cacheKey);
    }
}
