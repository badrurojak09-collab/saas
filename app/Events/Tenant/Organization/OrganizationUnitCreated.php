<?php

namespace App\Events\Tenant\Organization;

use App\Models\Tenant\OrganizationUnit;

class OrganizationUnitCreated
{
    public function __construct(public OrganizationUnit $organizationUnit) {}
}
