<?php

namespace App\Http\Middleware;

use App\Tenancy\Contracts\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function __construct(
        protected TenantResolver $tenantResolver
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil host domain (contoh: kampus.mysaasmp.test)
        $host = $request->getHost();

        // 2. Jalankan resolver tenant bawaan kamu
        // Sesuaikan nama method-nya jika di TenantResolverService kamu beda
        $tenant = $this->tenantResolver->resolveFromHost($host);

        if (! $tenant) {
            abort(404, 'Tenant tidak ditemukan.');
        }

        return $next($request);
    }
}
