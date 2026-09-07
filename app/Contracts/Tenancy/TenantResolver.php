<?php

namespace App\Contracts\Tenancy;

use Illuminate\Http\Request;

interface TenantResolver
{
    public function resolve(Request $request): ?IdentifiesTenant;
}
