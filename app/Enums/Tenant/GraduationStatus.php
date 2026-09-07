<?php

namespace App\Enums\Tenant;

enum GraduationStatus: string
{
    case Proposed = 'proposed';
    case Verified = 'verified';
    case Approved = 'approved';
    case Graduated = 'graduated';
    case Cancelled = 'cancelled';
}
