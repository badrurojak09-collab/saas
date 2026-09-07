<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\User;

class UserPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('tenant_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasPermissionTo('users.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->hasPermissionTo('users.update');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->hasPermissionTo('users.delete');
    }

    public function activate(User $user, User $model): bool
    {
        return $user->hasPermissionTo('users.activate');
    }

    public function suspend(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->hasPermissionTo('users.suspend');
    }
}
