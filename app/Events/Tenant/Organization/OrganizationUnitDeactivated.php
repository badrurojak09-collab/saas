<?php

namespace App\Events\Tenant\Organization;

use App\Models\Tenant\OrganizationUnit;

class OrganizationUnitDeactivated
{
    public function __construct(public OrganizationUnit $organizationUnit) {}
}
