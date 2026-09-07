<?php

namespace App\Policies\Landlord;

use App\Models\Landlord\Package;
use App\Models\Landlord\PlatformUser;

/**
 * Mengikuti Authorization Matrix (Implementation Spec V1 Sprint 02, §66):
 * super_admin => CRUD, billing => CRUD, operations => read-only, support => read-only.
 *
 * Laravel akan auto-discover policy ini karena namespace-nya mengikuti pola
 * App\Models\Landlord\Package -> App\Policies\Landlord\PackagePolicy.
 * Tidak perlu didaftarkan manual di provider.
 */
class PackagePolicy
{
    public function viewAny(PlatformUser $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'billing', 'operations', 'support']);
    }

    public function view(PlatformUser $user, Package $package): bool
    {
        return $this->viewAny($user);
    }

    public function create(PlatformUser $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'billing']);
    }

    public function update(PlatformUser $user, Package $package): bool
    {
        return $this->create($user);
    }

    public function delete(PlatformUser $user, Package $package): bool
    {
        return $this->create($user);
    }

    public function restore(PlatformUser $user, Package $package): bool
    {
        return $this->create($user);
    }

    public function forceDelete(PlatformUser $user, Package $package): bool
    {
        return $user->hasRole('super_admin');
    }
}
