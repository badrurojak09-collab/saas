<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\Student;
use App\Models\Tenant\User;

class StudentPolicy
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
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi', 'keuangan', 'operator']);
    }

    public function view(User $user, Student $student): bool
    {
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi', 'keuangan', 'operator']) || $user->id === $student->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'akademik']);
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasAnyRole(['admin', 'akademik']);
    }

    public function changeStatus(User $user, Student $student): bool
    {
        return $user->hasAnyRole(['admin', 'akademik']);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasRole('admin');
    }
}
