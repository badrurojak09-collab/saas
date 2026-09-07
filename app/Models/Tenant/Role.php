<?php

namespace App\Models\Tenant;

use Spatie\Permission\Models\Role as SpatieRole;

final class Role extends SpatieRole
{
    protected $connection = 'tenant';
}
