<?php

namespace App\Enums\Tenant;

enum GradeStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Published = 'published';
    case Locked = 'locked';
}
