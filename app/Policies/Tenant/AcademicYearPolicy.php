<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\AcademicYear;
use App\Models\Tenant\User;

class AcademicYearPolicy
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
        return true;
    }

    public function view(User $user, AcademicYear $academicYear): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'akademik']);
    }

    public function update(User $user, AcademicYear $academicYear): bool
    {
        return $user->hasAnyRole(['admin', 'akademik']);
    }

    public function delete(User $user, AcademicYear $academicYear): bool
    {
        return $user->hasRole('admin');
    }
}
