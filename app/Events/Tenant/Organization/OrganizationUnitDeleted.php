<?php

namespace App\Events\Tenant\Organization;

use App\Models\Tenant\OrganizationUnit;

class OrganizationUnitDeleted
{
    public function __construct(public OrganizationUnit $organizationUnit) {}
}
