<?php

namespace App\Enums\Tenant;

enum AcademicResultStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Published = 'published';
    case Locked = 'locked';
}
