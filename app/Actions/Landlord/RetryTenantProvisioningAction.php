<?php

namespace App\Actions\Landlord;

use App\Enums\Landlord\ProvisioningJobStatus;
use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Jobs\Tenant\ProvisionTenantJob;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Tenancy\Exceptions\Provisioning\TenantProvisioningException;

class RetryTenantProvisioningAction
{
    /**
     * Retry tenant provisioning by creating a new ProvisioningJob attempt.
     *
     * @throws TenantProvisioningException
     */
    public function execute(Tenant $tenant): ProvisioningJob
    {
        $database = $tenant->database;

        if ($tenant->status === TenantStatus::ACTIVE && $database?->status === TenantDatabaseStatus::READY) {
            throw new TenantProvisioningException(
                sprintf('Tenant [%s] is already active and its database is ready. Retry is not permitted.', $tenant->code)
            );
        }

        $lastJob = ProvisioningJob::query()
            ->where('tenant_id', $tenant->getKey())
            ->latest('started_at')
            ->first();

        $attempt = ($lastJob?->attempts ?? 0) + 1;

        /** @var ProvisioningJob $job */
        $job = ProvisioningJob::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => $attempt,
            'payload' => [
                'tenant_id' => (string) $tenant->getKey(),
                'database' => $database?->database,
                'retry' => true,
            ],
        ]);

        ProvisionTenantJob::dispatch((string) $tenant->getKey(), (string) $job->getKey());

        return $job;
    }
}
