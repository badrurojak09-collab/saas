<?php

namespace App\Jobs\Tenant;

use App\DTOs\Tenant\TenantAdminData;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Services\Tenant\TenantProvisioningService;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProvisionTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    /**
     * @param  array<string, mixed>|null  $adminDataArray
     */
    public function __construct(
        public readonly string $tenantId,
        public readonly ?string $provisioningJobId = null,
        public readonly ?array $adminDataArray = null,
    ) {
        $this->afterCommit = true;
        $this->onQueue(config('queue.tenant_queue', 'provisioning'));
    }

    public function handle(
        TenantProvisioningService $provisioningService,
        TenantConnectionManager $connectionManager,
        TenantContext $tenantContext,
    ): void {
        $tenant = Tenant::query()->findOrFail($this->tenantId);

        $provisioningJob = $this->provisioningJobId
            ? ProvisioningJob::query()->find($this->provisioningJobId)
            : null;

        $adminData = $this->adminDataArray ? TenantAdminData::fromArray($this->adminDataArray) : null;

        try {
            $provisioningService->provision(
                tenant: $tenant,
                provisioningJob: $provisioningJob,
                adminData: $adminData,
                attempt: $this->attempts(),
            );
        } finally {
            $connectionManager->disconnect();
            $tenantContext->clear();
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('Tenant provisioning exhausted all retry attempts.', [
            'tenant_id' => $this->tenantId,
            'provisioning_job_id' => $this->provisioningJobId,
            'error' => $exception->getMessage(),
        ]);
    }
}
