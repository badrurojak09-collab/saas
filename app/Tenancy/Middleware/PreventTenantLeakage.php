<?php

namespace App\Tenancy\Middleware;

use App\Tenancy\Contracts\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventTenantLeakage
{
    public function __construct(
        private readonly TenantManager $manager,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Perform any final cleanup tasks for Octane and long-running processes.
     */
    public function terminate(Request $request, Response $response): void
    {
        if ($this->manager->isInitialized()) {
            $this->manager->end();
        }
    }
}
