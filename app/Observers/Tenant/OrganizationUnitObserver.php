<?php

namespace App\Observers\Tenant;

use App\Events\Tenant\Organization\OrganizationUnitActivated;
use App\Events\Tenant\Organization\OrganizationUnitCreated;
use App\Events\Tenant\Organization\OrganizationUnitDeactivated;
use App\Events\Tenant\Organization\OrganizationUnitDeleted;
use App\Events\Tenant\Organization\OrganizationUnitUpdated;
use App\Models\Tenant\OrganizationUnit;
use Illuminate\Support\Facades\Cache;

class OrganizationUnitObserver
{
    public function created(OrganizationUnit $organizationUnit): void
    {
        Cache::forget("tenant:{$organizationUnit->perguruan_tinggi_id}:organization-tree");
        event(new OrganizationUnitCreated($organizationUnit));
    }

    public function updated(OrganizationUnit $organizationUnit): void
    {
        Cache::forget("tenant:{$organizationUnit->perguruan_tinggi_id}:organization-tree");
        event(new OrganizationUnitUpdated($organizationUnit));
    }

    public function deleted(OrganizationUnit $organizationUnit): void
    {
        Cache::forget("tenant:{$organizationUnit->perguruan_tinggi_id}:organization-tree");
        event(new OrganizationUnitDeleted($organizationUnit));
    }

    public function restored(OrganizationUnit $organizationUnit): void
    {
        Cache::forget("tenant:{$organizationUnit->perguruan_tinggi_id}:organization-tree");
    }

    public function activated(OrganizationUnit $organizationUnit): void
    {
        event(new OrganizationUnitActivated($organizationUnit));
    }

    public function deactivated(OrganizationUnit $organizationUnit): void
    {
        event(new OrganizationUnitDeactivated($organizationUnit));
    }
}
