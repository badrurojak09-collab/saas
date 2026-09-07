<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\Department;
use App\Models\Tenant\User;

class DepartmentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi']);
    }

    public function view(User $user, Department $department): bool
    {
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->hasRole('admin');
    }
}
