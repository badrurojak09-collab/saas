<?php

namespace App\Services\Tenant;

use App\Enums\Tenant\OrganizationMembershipType;
use App\Enums\Tenant\OrganizationUnitStatus;
use App\Models\Tenant\OrganizationMembership;
use App\Models\Tenant\OrganizationUnit;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class OrganizationMembershipService
{
    public function assignUser(
        User $user,
        OrganizationUnit $organizationUnit,
        OrganizationMembershipType $membershipType = OrganizationMembershipType::Member,
        bool $isPrimary = false,
    ): OrganizationMembership {
        if ($organizationUnit->status !== OrganizationUnitStatus::Active) {
            throw new InvalidArgumentException('Inactive organization units cannot receive new memberships.');
        }

        return DB::connection('tenant')->transaction(function () use ($user, $organizationUnit, $membershipType, $isPrimary): OrganizationMembership {
            $membership = OrganizationMembership::query()->firstOrNew([
                'user_id' => $user->getKey(),
                'organization_unit_id' => $organizationUnit->getKey(),
                'membership_type' => $membershipType,
            ]);
            $membership->starts_at ??= now();
            $membership->is_primary = $isPrimary;
            $membership->save();

            if ($isPrimary) {
                $this->setPrimary($membership);
            }

            return $membership->fresh();
        });
    }

    public function setPrimary(OrganizationMembership $membership): OrganizationMembership
    {
        return DB::connection('tenant')->transaction(function () use ($membership): OrganizationMembership {
            OrganizationMembership::query()
                ->where('user_id', $membership->user_id)
                ->lockForUpdate()
                ->update(['is_primary' => false]);

            $membership->forceFill(['is_primary' => true])->save();

            return $membership->fresh();
        });
    }

    public function endMembership(OrganizationMembership $membership): OrganizationMembership
    {
        $membership->forceFill([
            'ends_at' => now(),
            'is_primary' => false,
        ])->save();

        return $membership->fresh();
    }

    public function activeMemberships(User $user): Collection
    {
        return OrganizationMembership::query()
            ->where('user_id', $user->getKey())
            ->where(fn($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->get();
    }
}
