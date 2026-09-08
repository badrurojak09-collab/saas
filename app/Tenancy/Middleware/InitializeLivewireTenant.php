<?php

namespace App\Tenancy\Middleware;

use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use App\Tenancy\Exceptions\TenantDomainNotFoundException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class InitializeLivewireTenant
{
    public function __construct(
        private readonly TenantManager $manager,
        private readonly TenantResolver $resolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isLivewireRequest($request) || $this->manager->isInitialized()) {
            return $next($request);
        }

        try {
            $tenant = $this->resolver->resolveFromHost($request->getHost());
        } catch (TenantDomainNotFoundException) {
            // Livewire is also used by the landlord/admin panel.
            return $next($request);
        }

        $this->manager->initialize($tenant);

        try {
            return $next($request);
        } finally {
            $this->manager->end();
        }
    }

    private function isLivewireRequest(Request $request): bool
    {
        return $request->is('livewire/*', 'livewire-*/*')
            || $request->headers->has('X-Livewire');
    }
}
