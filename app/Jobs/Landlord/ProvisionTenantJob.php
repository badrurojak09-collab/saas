<?php

namespace App\Jobs\Landlord;

use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Services\Tenant\TenantProvisioningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ProvisionTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(
        public readonly string $tenantId,
        public readonly ?string $provisioningJobId = null,
    ) {
        $this->afterCommit = true;
        $this->onQueue(config('queue.tenant_queue', 'provisioning'));
    }

    public function handle(TenantProvisioningService $provisioningService): void
    {
        $tenant = Tenant::query()->findOrFail($this->tenantId);

        $job = $this->provisioningJobId
            ? ProvisioningJob::query()->find($this->provisioningJobId)
            : ProvisioningJob::query()->where('tenant_id', $tenant->getKey())->latest('created_at')->first();

        $provisioningService->provision(
            tenant: $tenant,
            provisioningJob: $job,
            attempt: $this->attempts(),
        );
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('Tenant provisioning exhausted retries.', ['tenant_id' => $this->tenantId, 'exception' => $exception]);
    }
}
