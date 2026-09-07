<?php

namespace App\Tenancy;

use App\Contracts\Tenancy\IdentifiesTenant;
use App\Contracts\Tenancy\TenantResolver;
use Illuminate\Http\Request;

final class NullTenantResolver implements TenantResolver
{
    public function resolve(Request $request): ?IdentifiesTenant
    {
        return null;
    }
}
