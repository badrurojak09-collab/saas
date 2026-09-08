<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\OrganizationUnit;
use App\Models\Tenant\User;

class OrganizationUnitPolicy
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
        return $user->hasPermissionTo('organization_units.view');
    }

    public function view(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->hasPermissionTo('organization_units.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('organization_units.create');
    }

    public function update(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->hasPermissionTo('organization_units.update');
    }

    public function delete(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->hasPermissionTo('organization_units.delete');
    }

    public function restore(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->hasPermissionTo('organization_units.update');
    }

    public function activate(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->hasPermissionTo('organization_units.activate');
    }

    public function deactivate(User $user, OrganizationUnit $organizationUnit): bool
    {
        return $user->hasPermissionTo('organization_units.deactivate');
    }
}
