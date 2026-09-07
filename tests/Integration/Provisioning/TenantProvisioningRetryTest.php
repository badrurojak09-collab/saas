<?php

namespace Tests\Integration\Provisioning;

use App\Actions\Landlord\CreateTenantAction;
use App\Actions\Landlord\RetryTenantProvisioningAction;
use App\DTOs\Tenant\ProvisionTenantData;
use App\DTOs\Tenant\TenantAdminData;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Events\Landlord\TenantCreated;
use App\Events\Landlord\TenantProvisioningCompleted;
use App\Events\Landlord\TenantProvisioningStarted;
use App\Jobs\Tenant\ProvisionTenantJob;
use App\Models\Landlord\Domain;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Models\Landlord\TenantSubscription;
use App\Services\Tenant\TenantProvisioningService;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;
use App\Tenancy\Exceptions\Provisioning\TenantProvisioningException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TenantProvisioningRetryTest extends TestCase
{
    private string $tempDbPath;

    private function cleanupTestTenants(): void
    {
        $tenants = Tenant::query()->where('code', 'like', 'RETRY_%')->get();
        foreach ($tenants as $t) {
            Domain::query()->where('tenant_id', $t->getKey())->forceDelete();
            ProvisioningJob::query()->where('tenant_id', $t->getKey())->forceDelete();
            TenantDatabase::query()->where('tenant_id', $t->getKey())->forceDelete();
            TenantSubscription::query()->where('tenant_id', $t->getKey())->forceDelete();
            $t->forceDelete();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDbPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_tenant_retry_'.uniqid().'.sqlite';
        $this->cleanupTestTenants();
    }

    protected function tearDown(): void
    {
        app(TenantConnectionManager::class)->disconnect();
        app(TenantContext::class)->clear();
        $this->cleanupTestTenants();
        if (file_exists($this->tempDbPath)) {
            @unlink($this->tempDbPath);
        }
        parent::tearDown();
    }

    public function test_retry_dispatches_new_job_attempt_when_previous_attempt_failed(): void
    {
        Queue::fake();
        Event::fake([TenantCreated::class]);

        $suffix = strtoupper(substr(uniqid(), -4));
        $code = 'RETRY_'.$suffix;
        $slug = 'retry-'.strtolower($suffix);

        $createAction = new CreateTenantAction;
        $dto = new ProvisionTenantData(
            name: 'Universitas Coba Lagi',
            code: $code,
            slug: $slug,
            database: $this->tempDbPath,
            driver: 'sqlite',
        );

        $tenant = $createAction->execute($dto);
        $tenant->database->update(['status' => TenantDatabaseStatus::FAILED]);

        // Prior failed job record
        ProvisioningJob::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::FAILED,
            'attempts' => 1,
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(9),
            'error_message' => 'Simulated prior failure',
        ]);

        $retryAction = new RetryTenantProvisioningAction;
        $newJob = $retryAction->execute($tenant);

        $this->assertInstanceOf(ProvisioningJob::class, $newJob);
        $this->assertSame(2, $newJob->attempts);
        $this->assertSame(ProvisioningJobStatus::PENDING, $newJob->status);

        Queue::assertPushed(ProvisionTenantJob::class, function (ProvisionTenantJob $job) use ($tenant, $newJob) {
            return $job->tenantId === (string) $tenant->getKey()
                && $job->provisioningJobId === (string) $newJob->getKey();
        });
    }

    public function test_retry_provisioning_succeeds_idempotently(): void
    {
        Event::fake([
            TenantCreated::class,
            TenantProvisioningStarted::class,
            TenantProvisioningCompleted::class,
        ]);

        $suffix = strtoupper(substr(uniqid(), -4));
        $code = 'RETRY_'.$suffix;
        $slug = 'retry-'.strtolower($suffix);

        $createAction = new CreateTenantAction;
        $admin = new TenantAdminData(
            name: 'Admin Retry',
            email: 'admin@retry.ac.id',
            username: 'admin_retry',
            password: 'RetryPassword123!',
        );
        $dto = new ProvisionTenantData(
            name: 'Universitas Coba Lagi Idempotent',
            code: $code,
            slug: $slug,
            database: $this->tempDbPath,
            driver: 'sqlite',
            admin: $admin,
        );

        $tenant = $createAction->execute($dto);
        $tenant->database->update(['status' => TenantDatabaseStatus::FAILED]);

        // Attempt 2 Job
        /** @var ProvisioningJob $job */
        $job = ProvisioningJob::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 2,
        ]);

        /** @var TenantProvisioningService $service */
        $service = app(TenantProvisioningService::class);
        $service->provision($tenant, $job, $admin, attempt: 2);

        $tenant->refresh();
        $database = $tenant->database->fresh();
        $job->refresh();

        $this->assertSame(TenantStatus::ACTIVE, $tenant->status);
        $this->assertSame(TenantDatabaseStatus::READY, $database->status);
        $this->assertSame(ProvisioningJobStatus::COMPLETED, $job->status);
        $this->assertSame(2, $job->attempts);

        // Attempting to retry an already ACTIVE + READY tenant is rejected
        $retryAction = new RetryTenantProvisioningAction;
        $this->expectException(TenantProvisioningException::class);
        $this->expectExceptionMessage('already active and its database is ready. Retry is not permitted.');
        $retryAction->execute($tenant);
    }
}
