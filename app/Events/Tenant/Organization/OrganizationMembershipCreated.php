<?php

namespace App\Events\Tenant\Organization;

use App\Models\Tenant\OrganizationMembership;

class OrganizationMembershipCreated
{
    public function __construct(public OrganizationMembership $membership) {}
}
