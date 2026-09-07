<?php

namespace App\Enums\Tenant;

enum CourseType: string
{
    case Mandatory = 'mandatory';
    case Elective = 'elective';
    case Optional = 'optional';
}
