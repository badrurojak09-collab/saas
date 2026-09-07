<?php

namespace App\Enums\Tenant;

enum EmploymentStatus: string
{
    case Permanent = 'permanent';
    case Contract = 'contract';
    case Honorary = 'honorary';
    case Guest = 'guest';
    case Retired = 'retired';
}
