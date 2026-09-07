<?php

namespace App\Tenancy\Queue;

use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use App\Tenancy\Exceptions\TenantContextMissingException;
use Closure;

class InitializeTenantForJob
{
    public function handle(object $job, Closure $next): mixed
    {
        if (! isset($job->tenantId) || empty($job->tenantId)) {
            throw new TenantContextMissingException(
                'Queued job ['.get_class($job).'] requires a valid tenantId property.'
            );
        }

        /** @var TenantResolver $resolver */
        $resolver = app(TenantResolver::class);

        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);

        $tenant = $resolver->resolveFromId($job->tenantId);

        $manager->initialize($tenant);

        try {
            return $next($job);
        } finally {
            $manager->end();
        }
    }
}
