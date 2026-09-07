<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\Course;
use App\Models\Tenant\User;

class CoursePolicy
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

    public function view(User $user, Course $course): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi']);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi']);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasRole('admin');
    }
}
