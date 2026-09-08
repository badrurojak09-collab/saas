<?php

namespace App\Actions\Tenant\Organization;

use App\Enums\Tenant\OrganizationUnitStatus;
use App\Models\Tenant\OrganizationUnit;

final class DeactivateOrganizationUnitAction
{
    public function execute(OrganizationUnit $unit): OrganizationUnit
    {
        $unit->update(['status' => OrganizationUnitStatus::Inactive]);

        return $unit->refresh();
    }
}
