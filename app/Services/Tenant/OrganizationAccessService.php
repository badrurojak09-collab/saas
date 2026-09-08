<?php

namespace App\Services\Tenant;

use App\Exceptions\Authorization\TenantAccessDeniedException;
use App\Models\Tenant\OrganizationMembership;
use App\Models\Tenant\OrganizationUnit;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Collection;

final class OrganizationAccessService
{
    public function canAccess(User $user, OrganizationUnit $organizationUnit): bool
    {
        if ($user->hasRole('tenant_admin')) {
            return true;
        }

        return OrganizationMembership::query()
            ->where('user_id', $user->getKey())
            ->where('organization_unit_id', $organizationUnit->getKey())
            ->get()
            ->contains(fn(OrganizationMembership $membership): bool => $membership->isActive());
    }

    public function authorize(User $user, OrganizationUnit $organizationUnit): void
    {
        if (! $this->canAccess($user, $organizationUnit)) {
            throw new TenantAccessDeniedException('User does not have access to this organization scope.');
        }
    }

    public function memberships(User $user): Collection
    {
        return OrganizationMembership::query()
            ->where('user_id', $user->getKey())
            ->where(function ($query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->whereNull('deleted_at')
            ->get();
    }
}
