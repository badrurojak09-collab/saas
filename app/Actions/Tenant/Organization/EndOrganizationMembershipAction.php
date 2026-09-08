<?php

namespace App\Actions\Tenant\Organization;

use App\Models\Tenant\OrganizationMembership;
use App\Services\Tenant\OrganizationMembershipService;

final class EndOrganizationMembershipAction
{
    public function __construct(private OrganizationMembershipService $service) {}

    public function execute(OrganizationMembership $membership): OrganizationMembership
    {
        return $this->service->endMembership($membership);
    }
}
