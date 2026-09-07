<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\StudyProgram;
use App\Models\Tenant\User;

class StudyProgramPolicy
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
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi', 'dosen']);
    }

    public function view(User $user, StudyProgram $studyProgram): bool
    {
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi', 'dosen']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, StudyProgram $studyProgram): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, StudyProgram $studyProgram): bool
    {
        return $user->hasRole('admin');
    }
}
