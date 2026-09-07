<?php

namespace App\Console\Commands;

use App\Enums\Landlord\ProvisioningJobStatus;
use App\Jobs\Tenant\ProvisionTenantJob;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Services\Tenant\TenantProvisioningService;
use Illuminate\Console\Command;
use Throwable;

class TenantProvisionCommand extends Command
{
    protected $signature = 'tenant:provision 
                            {tenant : Tenant code, slug, or ID} 
                            {--sync : Run provisioning synchronously instead of pushing to queue}';

    protected $description = 'Trigger provisioning for a tenant database and initial setup';

    public function handle(TenantProvisioningService $provisioningService): int
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

        $this->info(sprintf('Initiating provisioning for tenant: %s (%s)', $tenant->name, $tenant->code));

        if ($this->option('sync')) {
            $this->line('Running provisioning synchronously...');

            /** @var ProvisioningJob $job */
            $job = ProvisioningJob::query()->create([
                'tenant_id' => $tenant->getKey(),
                'job_type' => 'PROVISION_TENANT_SYNC',
                'status' => ProvisioningJobStatus::PENDING,
                'attempts' => 1,
                'payload' => [
                    'tenant_id' => (string) $tenant->getKey(),
                    'sync' => true,
                ],
            ]);

            try {
                $provisioningService->provision($tenant, $job);
                $this->info('Tenant provisioning completed successfully.');

                return self::SUCCESS;
            } catch (Throwable $e) {
                $this->error(sprintf('Provisioning failed: %s', $e->getMessage()));

                return self::FAILURE;
            }
        }

        /** @var ProvisioningJob $job */
        $job = ProvisioningJob::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 1,
            'payload' => [
                'tenant_id' => (string) $tenant->getKey(),
                'sync' => false,
            ],
        ]);

        ProvisionTenantJob::dispatch((string) $tenant->getKey(), (string) $job->getKey());

        $this->info(sprintf('Provisioning job dispatched to queue. Job ID: %s', $job->getKey()));

        return self::SUCCESS;
    }
}
