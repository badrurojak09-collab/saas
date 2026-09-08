<?php

namespace App\Services\Tenant;

use App\Enums\Tenant\OrganizationUnitType;
use App\Models\Tenant\OrganizationUnit;
use InvalidArgumentException;

final class OrganizationHierarchyService
{
    public function canHaveParent(OrganizationUnitType $child, OrganizationUnitType $parent): bool
    {
        if ($child === OrganizationUnitType::University) {
            return false;
        }

        if ($child === OrganizationUnitType::Faculty) {
            return $parent === OrganizationUnitType::University;
        }

        if ($child === OrganizationUnitType::StudyProgram) {
            return in_array($parent, [OrganizationUnitType::Faculty, OrganizationUnitType::Department], true);
        }

        return $parent === OrganizationUnitType::University
            || $parent === OrganizationUnitType::Faculty
            || $parent === OrganizationUnitType::Department;
    }

    public function validateParent(OrganizationUnit $parent, OrganizationUnitType $childType): void
    {
        if (! $this->canHaveParent($childType, $parent->type)) {
            throw new InvalidArgumentException('The selected organization parent is not valid for this unit type.');
        }
    }

    public function validateNoCycle(OrganizationUnit $unit, ?OrganizationUnit $parent): void
    {
        $current = $parent;

        while ($current) {
            if ($current->is($unit)) {
                throw new InvalidArgumentException('An organization unit cannot be its own ancestor.');
            }

            $current = $current->parent;
        }
    }
}
