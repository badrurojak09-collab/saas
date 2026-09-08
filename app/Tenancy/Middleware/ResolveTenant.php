<?php

namespace App\Tenancy\Middleware;

use App\Tenancy\Contracts\TenantResolver;
use App\Tenancy\Exceptions\TenantDomainNotFoundException;
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
        $tenant = null;

        if (app()->environment(['local', 'testing']) && $request->filled('tenant')) {
            $tenant = $this->resolver->resolveFromId((string) $request->query('tenant'));
        }

        try {
            $tenant ??= $this->resolver->resolveFromHost($request->getHost());
        } catch (TenantDomainNotFoundException $exception) {
            // Livewire also serves landlord/admin components. Those requests do
            // not have a tenant domain and must not be forced into tenant mode.
            if ($request->is('livewire/*', 'livewire-*/*') || $request->headers->has('X-Livewire')) {
                return $next($request);
            }

            throw $exception;
        }

        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
