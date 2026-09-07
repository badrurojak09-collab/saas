<?php

namespace App\Policies\Landlord;

use App\Models\Landlord\Addon;
use App\Models\Landlord\PlatformUser;

class AddonPolicy
{
    public function viewAny(PlatformUser $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'billing', 'operations', 'support']);
    }

    public function view(PlatformUser $user, Addon $addon): bool
    {
        return $this->viewAny($user);
    }

    public function create(PlatformUser $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'billing']);
    }

    public function update(PlatformUser $user, Addon $addon): bool
    {
        return $this->create($user);
    }

    public function delete(PlatformUser $user, Addon $addon): bool
    {
        return $this->create($user);
    }

    public function restore(PlatformUser $user, Addon $addon): bool
    {
        return $this->create($user);
    }

    public function forceDelete(PlatformUser $user, Addon $addon): bool
    {
        return $user->hasRole('super_admin');
    }
}
