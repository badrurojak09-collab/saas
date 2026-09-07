<?php

namespace App\Tenancy\Services;

use App\Contracts\Tenancy\IdentifiesTenant;
use App\Contracts\Tenancy\TenantResolver as LegacyTenantResolverContract;
use App\Enums\Landlord\TenantStatus;
use App\Models\Landlord\Domain;
use App\Models\Landlord\Tenant;
use App\Tenancy\Contracts\TenantResolver as TenantResolverContract;
use App\Tenancy\Exceptions\TenantDomainNotFoundException;
use App\Tenancy\Exceptions\TenantInactiveException;
use App\Tenancy\Exceptions\TenantNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TenantResolverService implements LegacyTenantResolverContract, TenantResolverContract
{
    /**
     * Resolve a Tenant instance from an incoming HTTP host or domain string.
     */
    public function resolveFromHost(string $host): Tenant
    {
        $domain = $this->normalizeDomain($host);

        $cacheEnabled = (bool) config('tenancy.cache.enabled', true);
        $cachePrefix = (string) config('tenancy.cache.prefix', 'tenancy.domain.');
        $cacheTtl = (int) config('tenancy.cache.ttl', 300);
        $cacheKey = $cachePrefix.$domain;

        if ($cacheEnabled && Cache::has($cacheKey)) {
            $tenantId = Cache::get($cacheKey);
            /** @var Tenant|null $cachedTenant */
            $cachedTenant = Tenant::query()->with(['database'])->find($tenantId);
            if ($cachedTenant && $cachedTenant->status === TenantStatus::ACTIVE) {
                return $cachedTenant;
            }

            Cache::forget($cacheKey);
        }

        /** @var Domain|null $domainRecord */
        $domainRecord = Domain::query()
            ->with(['tenant.database'])
            ->whereRaw('LOWER(domain) = ?', [$domain])
            ->where('is_verified', true)
            ->whereNotNull('verified_at')
            ->first();

        if (! $domainRecord || ! $domainRecord->tenant) {
            if (app()->isLocal()) {
                $localTenant = null;
                if ($tenantParam = request()?->query('tenant')) {
                    $localTenant = Tenant::query()->with(['database'])->where('code', $tenantParam)->orWhere('slug', $tenantParam)->first();
                }
                if (! $localTenant && in_array($domain, ['localhost', '127.0.0.1'])) {
                    $localTenant = Tenant::query()->with(['database'])->where('status', TenantStatus::ACTIVE)->first();
                }
                if ($localTenant && $localTenant->status === TenantStatus::ACTIVE) {
                    return $localTenant;
                }
            }

            throw new TenantDomainNotFoundException(
                "Domain [{$domain}] is not registered, verified, or active."
            );
        }

        $tenant = $domainRecord->tenant;

        if ($tenant->status !== TenantStatus::ACTIVE) {
            $statusValue = $tenant->status instanceof TenantStatus
                ? $tenant->status->value
                : (string) $tenant->status;

            throw new TenantInactiveException(
                "Tenant [{$tenant->code}] is not active [status: {$statusValue}]."
            );
        }

        if ($cacheEnabled) {
            Cache::put($cacheKey, (string) $tenant->getKey(), $cacheTtl);
        }

        return $tenant;
    }

    /**
     * Resolve a Tenant instance from a Tenant ID or Code.
     */
    public function resolveFromId(string $tenantId): Tenant
    {
        /** @var Tenant|null $tenant */
        $tenant = Tenant::query()
            ->with(['database'])
            ->where('id', $tenantId)
            ->orWhere('code', $tenantId)
            ->first();

        if (! $tenant) {
            throw new TenantNotFoundException("Tenant [{$tenantId}] not found.");
        }

        if ($tenant->status !== TenantStatus::ACTIVE) {
            $statusValue = $tenant->status instanceof TenantStatus
                ? $tenant->status->value
                : (string) $tenant->status;

            throw new TenantInactiveException(
                "Tenant [{$tenant->code}] is not active [status: {$statusValue}]."
            );
        }

        return $tenant;
    }

    /**
     * Backward-compatible resolve method for legacy HTTP requests.
     */
    public function resolve(Request $request): ?IdentifiesTenant
    {
        try {
            return $this->resolveFromHost($request->getHost());
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Normalize host string: lowercase, strip port, strip leading brackets, trim trailing dots.
     */
    public function normalizeDomain(string $host): string
    {
        $normalized = strtolower(trim($host));
        $normalized = trim($normalized, ' .[]');
        $normalized = preg_replace('/:\d+$/', '', $normalized);

        return trim((string) $normalized, ' .[]');
    }
}
