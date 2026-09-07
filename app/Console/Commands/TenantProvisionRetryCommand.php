<?php

namespace App\Console\Commands;

use App\Actions\Landlord\RetryTenantProvisioningAction;
use App\Models\Landlord\Tenant;
use Illuminate\Console\Command;
use Throwable;

class TenantProvisionRetryCommand extends Command
{
    protected $signature = 'tenant:provision-retry {tenant : Tenant code, slug, or ID}';

    protected $description = 'Retry provisioning for a failed tenant';

    public function handle(RetryTenantProvisioningAction $retryAction): int
    {
        $tenantIdentifier = (string) $this->argument('tenant');

        /** @var Tenant|null $tenant */
        $tenant = Tenant::query()
            ->where('code', $tenantIdentifier)
            ->orWhere('slug', $tenantIdentifier)
            ->orWhere('id', $tenantIdentifier)
            ->first();

        if (! $tenant) {
            $this->error(sprintf("Tenant '%s' not found.", $tenantIdentifier));

            return self::FAILURE;
        }

        try {
            $job = $retryAction->execute($tenant);
            $this->info(sprintf(
                'Provisioning retry dispatched for tenant [%s]. New Attempt: %d (Job ID: %s)',
                $tenant->code,
                $job->attempts,
                $job->getKey()
            ));

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error(sprintf('Retry failed: %s', $e->getMessage()));

            return self::FAILURE;
        }
    }
}
