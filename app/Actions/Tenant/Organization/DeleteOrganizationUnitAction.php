<?php

namespace App\Actions\Tenant\Organization;

use App\Models\Tenant\OrganizationUnit;
use InvalidArgumentException;

final class DeleteOrganizationUnitAction
{
    public function execute(OrganizationUnit $unit): void
    {
        if ($unit->children()->exists() || $unit->memberships()->whereNull('ends_at')->exists()) {
            throw new InvalidArgumentException('Organization units with children or active memberships cannot be deleted.');
        }

        $unit->delete();
    }
}
