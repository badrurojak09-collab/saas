<?php

namespace App\Observers\Tenant;

use App\Events\Tenant\Organization\OrganizationMembershipCreated;
use App\Events\Tenant\Organization\OrganizationMembershipUpdated;
use App\Models\Tenant\OrganizationMembership;
use Illuminate\Support\Facades\Cache;

class OrganizationMembershipObserver
{
    public function created(OrganizationMembership $membership): void
    {
        $this->invalidateCache($membership);
        event(new OrganizationMembershipCreated($membership));
    }

    public function updated(OrganizationMembership $membership): void
    {
        $this->invalidateCache($membership);
        event(new OrganizationMembershipUpdated($membership));
    }

    public function deleted(OrganizationMembership $membership): void
    {
        $this->invalidateCache($membership);
    }

    public function restored(OrganizationMembership $membership): void
    {
        $this->invalidateCache($membership);
    }

    private function invalidateCache(OrganizationMembership $membership): void
    {
        $tenantId = $membership->organizationUnit?->perguruan_tinggi_id;
        if ($tenantId) {
            Cache::forget("tenant:{$tenantId}:user:{$membership->user_id}:organizations");
        }
    }
}
