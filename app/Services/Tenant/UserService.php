<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\AssignRoleAction;
use App\Actions\Tenant\CreateUserAction;
use App\Actions\Tenant\UpdateUserAction;
use App\DTOs\Tenant\CreateUserData;
use App\DTOs\Tenant\UpdateUserData;
use App\Models\Tenant\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        protected CreateUserAction $createUserAction,
        protected UpdateUserAction $updateUserAction,
        protected AssignRoleAction $assignRoleAction,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::query()->latest()->paginate($perPage);
    }

    public function create(CreateUserData $data): User
    {
        return $this->createUserAction->execute($data);
    }

    public function update(User $user, UpdateUserData $data): User
    {
        return $this->updateUserAction->execute($user, $data);
    }

    public function assignRoles(User $user, array|string $roles): User
    {
        return $this->assignRoleAction->execute($user, $roles);
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}
