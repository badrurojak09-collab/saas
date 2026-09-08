<?php

namespace App\Tenancy\Middleware;

use App\Models\Landlord\Tenant;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use App\Tenancy\Exceptions\TenantNotFoundException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenant
{
    public function __construct(
        private readonly TenantManager $manager,
        private readonly TenantResolver $resolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->manager->isInitialized()) {
            return $next($request);
        }

        /** @var Tenant|null $tenant */
        $tenant = $request->attributes->get('tenant');

        if (! $tenant) {
            $tenant = $this->resolver->resolveFromHost($request->getHost());
            $request->attributes->set('tenant', $tenant);
        }

        if (! $tenant) {
            if ($this->isLivewireRequest($request)) {
                return $next($request);
            }

            throw new TenantNotFoundException('No active tenant could be resolved for this request.');
        }

        try {
            $this->manager->initialize($tenant);
        } catch (\Throwable $exception) {
            Log::error('Tenant initialization failed before panel request.', [
                'host' => $request->getHost(),
                'path' => $request->path(),
                'is_livewire' => $this->isLivewireRequest($request),
                'tenant_id' => $tenant->getKey(),
                'tenant_code' => $tenant->code,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

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
