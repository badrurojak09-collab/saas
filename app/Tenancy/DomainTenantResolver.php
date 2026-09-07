<?php

namespace App\Tenancy;

use App\Contracts\Tenancy\IdentifiesTenant;
use App\Contracts\Tenancy\TenantResolver;
use App\Tenancy\Services\TenantResolverService;
use Illuminate\Http\Request;

/**
 * Backward compatibility wrapper for DomainTenantResolver.
 */
final class DomainTenantResolver implements TenantResolver
{
    private TenantResolverService $resolver;

    public function __construct()
    {
        $this->resolver = app(TenantResolverService::class);
    }

    public function resolve(Request $request): ?IdentifiesTenant
    {
        return $this->resolver->resolve($request);
    }
}
