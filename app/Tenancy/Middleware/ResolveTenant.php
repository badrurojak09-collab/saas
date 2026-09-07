<?php

namespace App\Tenancy\Middleware;

use App\Tenancy\Contracts\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(
        private readonly TenantResolver $resolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolver->resolveFromHost($request->getHost());

        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
