<?php

namespace App\Services\Tenant;

use App\DTOs\Tenant\TenantAdminData;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Enums\Landlord\ProvisioningStep;
use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Events\Landlord\TenantProvisioningCompleted;
use App\Events\Landlord\TenantProvisioningFailed;
use App\Events\Landlord\TenantProvisioningStarted;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;
use App\Tenancy\Exceptions\Provisioning\TenantProvisioningException;
use App\Tenancy\Provisioning\TenantProvisioningContext;
use App\Tenancy\Provisioning\TenantProvisioningLock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class TenantProvisioningService
{
    public function __construct(
        protected TenantProvisioningLock $lock,
        protected TenantProvisioningContext $context,
        protected TenantDatabaseService $databaseService,
        protected TenantMigrationService $migrationService,
        protected TenantSeederService $seederService,
        protected TenantAdminProvisioningService $adminService,
        protected TenantHealthCheckService $healthCheckService,
        protected TenantConnectionManager $connectionManager,
        protected TenantContext $tenantContext,
    ) {}

    /**
     * Provision a tenant through the deterministic 10-step lifecycle.
     *
     * @throws TenantProvisioningException
     */
    public function provision(
        Tenant $tenant,
        ?ProvisioningJob $provisioningJob = null,
        ?TenantAdminData $adminData = null,
        int $attempt = 1
    ): void {
        $tenantId = (string) $tenant->getKey();
        $this->lock->acquire($tenantId);

        $jobId = $provisioningJob ? (string) $provisioningJob->getKey() : null;
        $this->context->start($tenantId, $jobId, $attempt);

        /** @var TenantDatabase|null $database */
        $database = TenantDatabase::query()->where('tenant_id', $tenantId)->where('is_primary', true)->first();

        if (! $database) {
            $this->lock->release($tenantId);
            throw new TenantProvisioningException(
                sprintf('Tenant [%s] does not have primary database metadata configured.', $tenant->code)
            );
        }

        try {
            $this->tenantContext->set($tenant);

            // 1. Mark status as PROVISIONING / RUNNING
            $tenant->update(['status' => TenantStatus::PROVISIONING]);
            $database->update(['status' => TenantDatabaseStatus::CREATING]);

            if ($provisioningJob) {
                $provisioningJob->update([
                    'status' => ProvisioningJobStatus::RUNNING,
                    'started_at' => now(),
                    'attempts' => $attempt,
                    'payload' => [
                        'tenant_id' => $tenantId,
                        'database' => $database->database,
                    ],
                ]);
            }

            event(new TenantProvisioningStarted($tenantId, $jobId));

            // Step 1: Create physical database
            $this->context->setStep(ProvisioningStep::CREATE_DATABASE);
            $stepStart = microtime(true);
            $this->databaseService->createPhysicalDatabase($database);
            $this->context->recordStepTiming(ProvisioningStep::CREATE_DATABASE, round((microtime(true) - $stepStart) * 1000, 2));

            // Step 2: Configure dynamic connection and run tenant migrations
            $this->context->setStep(ProvisioningStep::MIGRATE_DATABASE);
            $database->update(['status' => TenantDatabaseStatus::MIGRATING]);
            $stepStart = microtime(true);
            $this->migrationService->migrate($database);
            $this->context->recordStepTiming(ProvisioningStep::MIGRATE_DATABASE, round((microtime(true) - $stepStart) * 1000, 2));

            // Step 3: Run idempotent tenant seeders
            $this->context->setStep(ProvisioningStep::SEED_DATABASE);
            $stepStart = microtime(true);
            $this->seederService->seed($database);
            $this->context->recordStepTiming(ProvisioningStep::SEED_DATABASE, round((microtime(true) - $stepStart) * 1000, 2));

            // Step 4: Provision initial tenant administrator
            $this->context->setStep(ProvisioningStep::CREATE_ADMIN);
            $stepStart = microtime(true);
            $this->adminService->provision($database, $adminData);
            $this->context->recordStepTiming(ProvisioningStep::CREATE_ADMIN, round((microtime(true) - $stepStart) * 1000, 2));

            // Step 5: Tenant health check (Level 1, 2, 3)
            $this->context->setStep(ProvisioningStep::HEALTH_CHECK);
            $stepStart = microtime(true);
            $this->healthCheckService->assertHealthy($database);
            $this->context->recordStepTiming(ProvisioningStep::HEALTH_CHECK, round((microtime(true) - $stepStart) * 1000, 2));

            // Step 6: Final Landlord Transaction (READY = ACTIVE)
            $this->context->setStep(ProvisioningStep::COMPLETE);
            DB::connection('landlord')->transaction(function () use ($tenant, $database, $provisioningJob): void {
                $database->update([
                    'status' => TenantDatabaseStatus::READY,
                    'last_migrated_at' => now(),
                ]);

                $tenant->update([
                    'status' => TenantStatus::ACTIVE,
                ]);

                if ($provisioningJob) {
                    $provisioningJob->update([
                        'status' => ProvisioningJobStatus::COMPLETED,
                        'completed_at' => now(),
                    ]);
                }
            });

            event(new TenantProvisioningCompleted($tenantId, $jobId));
        } catch (Throwable $e) {
            // Failure flow: preserve DB for diagnosis, update status to FAILED/PENDING
            $database->update(['status' => TenantDatabaseStatus::FAILED]);
            $tenant->update(['status' => TenantStatus::PENDING]);

            if ($provisioningJob) {
                $provisioningJob->update([
                    'status' => ProvisioningJobStatus::FAILED,
                    'completed_at' => now(),
                    'error_message' => $e->getMessage(),
                    'attempts' => $attempt,
                ]);
            }

            Log::error('Tenant provisioning failed.', [
                'tenant_id' => $tenantId,
                'step' => $this->context->getStep()?->value,
                'error' => $e->getMessage(),
            ]);

            event(new TenantProvisioningFailed($tenantId, $jobId, $e->getMessage()));

            if ($e instanceof TenantProvisioningException) {
                throw $e;
            }

            throw new TenantProvisioningException(
                sprintf('Tenant provisioning failed: %s', $e->getMessage()),
                $this->context->getStep(),
                $e
            );
        } finally {
            $this->connectionManager->disconnect();
            $this->tenantContext->clear();
            $this->lock->release($tenantId);
        }
    }
}
