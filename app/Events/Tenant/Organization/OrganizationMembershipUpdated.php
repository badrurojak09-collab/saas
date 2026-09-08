<?php

namespace App\Events\Tenant\Organization;

use App\Models\Tenant\OrganizationMembership;

class OrganizationMembershipUpdated
{
    public function __construct(public OrganizationMembership $membership) {}
}
