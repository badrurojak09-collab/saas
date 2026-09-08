<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\OrganizationMembership;
use App\Models\Tenant\User;

class OrganizationMembershipPolicy
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
        return $user->hasPermissionTo('organization_memberships.view');
    }

    public function view(User $user, OrganizationMembership $membership): bool
    {
        return $user->hasPermissionTo('organization_memberships.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('organization_memberships.create');
    }

    public function update(User $user, OrganizationMembership $membership): bool
    {
        return $user->hasPermissionTo('organization_memberships.update');
    }

    public function delete(User $user, OrganizationMembership $membership): bool
    {
        return $user->hasPermissionTo('organization_memberships.delete');
    }
}
