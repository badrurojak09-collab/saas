<?php

namespace App\Enums\Tenant;

enum FeeBillingFrequency: string
{
    case OneTime = 'one_time';
    case PerSemester = 'per_semester';
    case Monthly = 'monthly';
    case Annual = 'annual';
}
