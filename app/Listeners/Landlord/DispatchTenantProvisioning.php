<?php

namespace App\Listeners\Landlord;

use App\Enums\Landlord\ProvisioningJobStatus;
use App\Events\Landlord\TenantCreated;
use App\Jobs\Tenant\ProvisionTenantJob;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;

class DispatchTenantProvisioning
{
    public function handle(TenantCreated $event): void
    {
        $tenant = Tenant::query()->find($event->tenantId);

        if (! $tenant) {
            return;
        }

        $database = $tenant->database;

        /** @var ProvisioningJob $job */
        $job = ProvisioningJob::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 0,
            'payload' => [
                'tenant_id' => $tenant->getKey(),
                'database' => $database?->database,
            ],
        ]);

        $adminDataArray = $event->adminData?->toArray();

        ProvisionTenantJob::dispatch($tenant->getKey(), (string) $job->getKey(), $adminDataArray)
            ->afterCommit();
    }
}
