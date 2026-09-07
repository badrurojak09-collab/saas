<?php

namespace App\Enums\Tenant;

enum GradingType: string
{
    case StandardLetter = 'standard_letter';
    case PassFail = 'pass_fail';
    case Scale100 = 'scale_100';
}
