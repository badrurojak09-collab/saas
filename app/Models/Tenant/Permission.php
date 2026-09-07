<?php

namespace App\Models\Tenant;

use Spatie\Permission\Models\Permission as SpatiePermission;

final class Permission extends SpatiePermission
{
    protected $connection = 'tenant';
}
