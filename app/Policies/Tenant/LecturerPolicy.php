<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\Lecturer;
use App\Models\Tenant\User;

class LecturerPolicy
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

    public function view(User $user, Lecturer $lecturer): bool
    {
        return $user->hasAnyRole(['admin', 'akademik', 'kaprodi']) || $user->id === $lecturer->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'akademik']);
    }

    public function update(User $user, Lecturer $lecturer): bool
    {
        return $user->hasAnyRole(['admin', 'akademik']) || $user->id === $lecturer->user_id;
    }

    public function delete(User $user, Lecturer $lecturer): bool
    {
        return $user->hasRole('admin');
    }
}
