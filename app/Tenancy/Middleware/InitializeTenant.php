<?php

namespace App\Tenancy\Middleware;

use App\Models\Landlord\Tenant;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use App\Tenancy\Exceptions\TenantNotFoundException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenant
{
    public function __construct(
        private readonly TenantManager $manager,
        private readonly TenantResolver $resolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Tenant|null $tenant */
        $tenant = $request->attributes->get('tenant');

        if (! $tenant) {
            $tenant = $this->resolver->resolveFromHost($request->getHost());
            $request->attributes->set('tenant', $tenant);
        }

        if (! $tenant) {
            throw new TenantNotFoundException('No active tenant could be resolved for this request.');
        }

        $this->manager->initialize($tenant);

        try {
            return $next($request);
        } finally {
            $this->manager->end();
        }
    }
}
