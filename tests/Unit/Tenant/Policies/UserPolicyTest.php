<?php

namespace Tests\Unit\Tenant\Policies;

use App\Models\Tenant\User;
use App\Policies\Tenant\UserPolicy;
use Tests\TestCase;

final class UserPolicyTest extends TestCase
{
    public function test_tenant_admin_bypasses_all_policy_checks_via_before(): void
    {
        $policy = new UserPolicy;
        $admin = $this->createMock(User::class);
        $admin->method('hasRole')->with('tenant_admin')->willReturn(true);

        $this->assertTrue($policy->before($admin, 'delete'));
        $this->assertTrue($policy->before($admin, 'update'));
    }

    public function test_view_any_checks_permission(): void
    {
        $policy = new UserPolicy;
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasPermissionTo')
            ->with('users.view')
            ->willReturn(true);

        $this->assertTrue($policy->viewAny($user));
    }

    public function test_view_allows_self_or_permission(): void
    {
        $policy = new UserPolicy;

        $self = new User;
        $self->id = 'user-1';

        $other = new User;
        $other->id = 'user-2';

        $this->assertTrue($policy->view($self, $self));

        $userWithPerm = $this->createMock(User::class);
        $userWithPerm->id = 'user-1';
        $userWithPerm->method('hasPermissionTo')->with('users.view')->willReturn(true);

        $this->assertTrue($policy->view($userWithPerm, $other));
    }

    public function test_create_checks_permission(): void
    {
        $policy = new UserPolicy;
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasPermissionTo')
            ->with('users.create')
            ->willReturn(true);

        $this->assertTrue($policy->create($user));
    }

    public function test_update_allows_self_or_permission(): void
    {
        $policy = new UserPolicy;

        $self = new User;
        $self->id = 'user-1';

        $other = new User;
        $other->id = 'user-2';

        $this->assertTrue($policy->update($self, $self));

        $userWithPerm = $this->createMock(User::class);
        $userWithPerm->id = 'user-1';
        $userWithPerm->method('hasPermissionTo')->with('users.update')->willReturn(true);

        $this->assertTrue($policy->update($userWithPerm, $other));
    }

    public function test_delete_prevents_self_deletion(): void
    {
        $policy = new UserPolicy;

        $self = new User;
        $self->id = 'user-1';

        $other = new User;
        $other->id = 'user-2';

        // Cannot delete self even if has permission
        $selfMock = $this->createMock(User::class);
        $selfMock->id = 'user-1';
        $selfMock->method('hasPermissionTo')->with('users.delete')->willReturn(true);

        $this->assertFalse($policy->delete($selfMock, $selfMock));

        // Can delete other if has permission
        $this->assertTrue($policy->delete($selfMock, $other));
    }

    public function test_activate_checks_permission(): void
    {
        $policy = new UserPolicy;
        $user = $this->createMock(User::class);
        $target = new User;

        $user->expects($this->once())
            ->method('hasPermissionTo')
            ->with('users.activate')
            ->willReturn(true);

        $this->assertTrue($policy->activate($user, $target));
    }

    public function test_suspend_prevents_self_suspension(): void
    {
        $policy = new UserPolicy;

        $self = $this->createMock(User::class);
        $self->id = 'user-1';
        $self->method('hasPermissionTo')->with('users.suspend')->willReturn(true);

        $other = new User;
        $other->id = 'user-2';

        $this->assertFalse($policy->suspend($self, $self));
        $this->assertTrue($policy->suspend($self, $other));
    }
}
