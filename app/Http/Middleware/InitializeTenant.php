<?php

namespace App\Http\Middleware;

use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use App\Tenancy\Middleware\InitializeTenant as TenancyInitializeTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backward compatibility alias for InitializeTenant middleware.
 */
final class InitializeTenant
{
    private TenancyInitializeTenant $middleware;

    public function __construct(TenantManager $manager, TenantResolver $resolver)
    {
        $this->middleware = new TenancyInitializeTenant($manager, $resolver);
    }

    public function handle(Request $request, Closure $next): Response
    {
        return $this->middleware->handle($request, $next);
    }
}
