<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\Faculty;
use App\Models\Tenant\User;

class FacultyPolicy
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

    public function view(User $user, Faculty $faculty): bool
    {
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Faculty $faculty): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Faculty $faculty): bool
    {
        return $user->hasRole('admin');
    }
}
