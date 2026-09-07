<?php

namespace Tests\Unit\Tenant\Services;

use App\Actions\Tenant\ActivateTenantUserAction;
use App\Actions\Tenant\CreateTenantUserAction;
use App\Actions\Tenant\DeleteTenantUserAction;
use App\Actions\Tenant\SuspendTenantUserAction;
use App\Actions\Tenant\UpdateTenantUserAction;
use App\DTOs\Tenant\CreateTenantUserData;
use App\DTOs\Tenant\UpdateTenantUserData;
use App\Enums\Tenant\UserStatus;
use App\Models\Tenant\User;
use App\Services\Tenant\TenantUserService;
use Tests\TestCase;

final class TenantUserServiceTest extends TestCase
{
    public function test_create_delegates_to_create_action(): void
    {
        $createAction = $this->createMock(CreateTenantUserAction::class);
        $updateAction = $this->createMock(UpdateTenantUserAction::class);
        $suspendAction = $this->createMock(SuspendTenantUserAction::class);
        $activateAction = $this->createMock(ActivateTenantUserAction::class);
        $deleteAction = $this->createMock(DeleteTenantUserAction::class);

        $dto = new CreateTenantUserData(
            name: 'John Doe',
            email: 'john@test.com',
            password: 'Password123!',
        );
        $user = new User;
        $user->id = 'u-001';

        $createAction->expects($this->once())
            ->method('execute')
            ->with($dto, 'admin-id')
            ->willReturn($user);

        $service = new TenantUserService($createAction, $updateAction, $suspendAction, $activateAction, $deleteAction);

        $result = $service->create($dto, 'admin-id');
        $this->assertSame($user, $result);
    }

    public function test_update_delegates_to_update_action(): void
    {
        $createAction = $this->createMock(CreateTenantUserAction::class);
        $updateAction = $this->createMock(UpdateTenantUserAction::class);
        $suspendAction = $this->createMock(SuspendTenantUserAction::class);
        $activateAction = $this->createMock(ActivateTenantUserAction::class);
        $deleteAction = $this->createMock(DeleteTenantUserAction::class);

        $dto = new UpdateTenantUserData(name: 'Updated Name');
        $user = new User;
        $user->id = 'u-001';

        $updateAction->expects($this->once())
            ->method('execute')
            ->with($user, $dto, 'admin-id')
            ->willReturn($user);

        $service = new TenantUserService($createAction, $updateAction, $suspendAction, $activateAction, $deleteAction);

        $result = $service->update($user, $dto, 'admin-id');
        $this->assertSame($user, $result);
    }

    public function test_suspend_delegates_to_suspend_action(): void
    {
        $createAction = $this->createMock(CreateTenantUserAction::class);
        $updateAction = $this->createMock(UpdateTenantUserAction::class);
        $suspendAction = $this->createMock(SuspendTenantUserAction::class);
        $activateAction = $this->createMock(ActivateTenantUserAction::class);
        $deleteAction = $this->createMock(DeleteTenantUserAction::class);

        $user = new User;
        $user->id = 'u-001';
        $user->status = UserStatus::Suspended;

        $suspendAction->expects($this->once())
            ->method('execute')
            ->with($user, 'admin-id')
            ->willReturn($user);

        $service = new TenantUserService($createAction, $updateAction, $suspendAction, $activateAction, $deleteAction);

        $result = $service->suspend($user, 'admin-id');
        $this->assertSame(UserStatus::Suspended, $result->status);
    }

    public function test_activate_delegates_to_activate_action(): void
    {
        $createAction = $this->createMock(CreateTenantUserAction::class);
        $updateAction = $this->createMock(UpdateTenantUserAction::class);
        $suspendAction = $this->createMock(SuspendTenantUserAction::class);
        $activateAction = $this->createMock(ActivateTenantUserAction::class);
        $deleteAction = $this->createMock(DeleteTenantUserAction::class);

        $user = new User;
        $user->id = 'u-001';
        $user->status = UserStatus::Active;

        $activateAction->expects($this->once())
            ->method('execute')
            ->with($user, 'admin-id')
            ->willReturn($user);

        $service = new TenantUserService($createAction, $updateAction, $suspendAction, $activateAction, $deleteAction);

        $result = $service->activate($user, 'admin-id');
        $this->assertSame(UserStatus::Active, $result->status);
    }

    public function test_delete_delegates_to_delete_action(): void
    {
        $createAction = $this->createMock(CreateTenantUserAction::class);
        $updateAction = $this->createMock(UpdateTenantUserAction::class);
        $suspendAction = $this->createMock(SuspendTenantUserAction::class);
        $activateAction = $this->createMock(ActivateTenantUserAction::class);
        $deleteAction = $this->createMock(DeleteTenantUserAction::class);

        $user = new User;
        $user->id = 'u-001';

        $deleteAction->expects($this->once())
            ->method('execute')
            ->with($user, 'admin-id');

        $service = new TenantUserService($createAction, $updateAction, $suspendAction, $activateAction, $deleteAction);

        $service->delete($user, 'admin-id');
        $this->assertTrue(true);
    }
}
