<?php

namespace App\Services\Tenant;

use App\Exceptions\Authorization\TenantAccessDeniedException;
use App\Models\Tenant\OrganizationMembership;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Collection;

final class OrganizationAccessService
{
    public function canAccess(User $user, string $organizationType, string $organizationId): bool
    {
        if ($user->hasRole('tenant_admin')) {
            return true;
        }

        return OrganizationMembership::query()
            ->where('user_id', $user->getKey())
            ->where('organization_type', $organizationType)
            ->where('organization_id', $organizationId)
            ->get()
            ->contains(fn (OrganizationMembership $membership): bool => $membership->isActive());
    }

    public function authorize(User $user, string $organizationType, string $organizationId): void
    {
        if (! $this->canAccess($user, $organizationType, $organizationId)) {
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
            ->get();
    }
}
