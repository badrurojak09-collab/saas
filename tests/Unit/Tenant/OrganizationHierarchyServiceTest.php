<?php

namespace Tests\Unit\Tenant;

use App\Enums\Tenant\OrganizationUnitType;
use App\Services\Tenant\OrganizationHierarchyService;
use PHPUnit\Framework\TestCase;

class OrganizationHierarchyServiceTest extends TestCase
{
    public function test_faculty_can_be_child_of_university(): void
    {
        $service = new OrganizationHierarchyService();

        $this->assertTrue($service->canHaveParent(OrganizationUnitType::Faculty, OrganizationUnitType::University));
    }

    public function test_university_cannot_be_child_of_faculty(): void
    {
        $service = new OrganizationHierarchyService();

        $this->assertFalse($service->canHaveParent(OrganizationUnitType::University, OrganizationUnitType::Faculty));
    }
}
