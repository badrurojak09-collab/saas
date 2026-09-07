<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\ActivateTenantUserAction;
use App\Actions\Tenant\CreateTenantUserAction;
use App\Actions\Tenant\DeleteTenantUserAction;
use App\Actions\Tenant\SuspendTenantUserAction;
use App\Actions\Tenant\UpdateTenantUserAction;
use App\DTOs\Tenant\CreateTenantUserData;
use App\DTOs\Tenant\UpdateTenantUserData;
use App\Models\Tenant\User;

class TenantUserService
{
    public function __construct(
        protected CreateTenantUserAction $createAction,
        protected UpdateTenantUserAction $updateAction,
        protected SuspendTenantUserAction $suspendAction,
        protected ActivateTenantUserAction $activateAction,
        protected DeleteTenantUserAction $deleteAction,
    ) {}

    public function create(CreateTenantUserData $data, ?string $performedBy = null): User
    {
        return $this->createAction->execute($data, $performedBy);
    }

    public function update(User $user, UpdateTenantUserData $data, ?string $performedBy = null): User
    {
        return $this->updateAction->execute($user, $data, $performedBy);
    }

    public function suspend(User $user, ?string $performedBy = null): User
    {
        return $this->suspendAction->execute($user, $performedBy);
    }

    public function activate(User $user, ?string $performedBy = null): User
    {
        return $this->activateAction->execute($user, $performedBy);
    }

    public function delete(User $user, ?string $performedBy = null): void
    {
        $this->deleteAction->execute($user, $performedBy);
    }
}
