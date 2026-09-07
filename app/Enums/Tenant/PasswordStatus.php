<?php

namespace App\Enums\Tenant;

enum PasswordStatus: string
{
    case Valid = 'valid';
    case Expired = 'expired';
    case NeedsReset = 'needs_reset';
}
