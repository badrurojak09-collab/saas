<?php

namespace App\Enums\Tenant;

enum OrganizationUnitStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Inactive = 'inactive';
}
