<?php

namespace Tests\Integration\Provisioning;

use App\Actions\Landlord\CreateTenantAction;
use App\DTOs\Tenant\ProvisionTenantData;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Events\Landlord\TenantCreated;
use App\Events\Landlord\TenantProvisioningFailed;
use App\Events\Landlord\TenantProvisioningStarted;
use App\Models\Landlord\Domain;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Models\Landlord\TenantSubscription;
use App\Services\Tenant\TenantMigrationService;
use App\Services\Tenant\TenantProvisioningService;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;
use App\Tenancy\Exceptions\Provisioning\TenantMigrationException;
use App\Tenancy\Exceptions\Provisioning\TenantProvisioningException;
use App\Tenancy\Provisioning\TenantProvisioningLock;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class TenantProvisioningFailureTest extends TestCase
{
    private string $tempDbPath;

    private function cleanupTestTenants(): void
    {
        $tenants = Tenant::query()->where('code', 'like', 'FAIL_%')->get();
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
        $this->tempDbPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_tenant_fail_'.uniqid().'.sqlite';
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

    public function test_provisioning_failure_preserves_database_and_marks_failed(): void
    {
        Event::fake([
            TenantCreated::class,
            TenantProvisioningStarted::class,
            TenantProvisioningFailed::class,
        ]);

        $suffix = strtoupper(substr(uniqid(), -4));
        $code = 'FAIL_'.$suffix;
        $slug = 'fail-'.strtolower($suffix);

        $createAction = new CreateTenantAction;
        $dto = new ProvisionTenantData(
            name: 'Universitas Gagal Uji',
            code: $code,
            slug: $slug,
            database: $this->tempDbPath,
            driver: 'sqlite',
        );

        $tenant = $createAction->execute($dto);

        /** @var ProvisioningJob $job */
        $job = ProvisioningJob::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 1,
        ]);

        // Mock TenantMigrationService to simulate a failure during migration step
        $failingMigrationService = Mockery::mock(TenantMigrationService::class);
        $failingMigrationService->shouldReceive('migrate')
            ->once()
            ->andThrow(new TenantMigrationException('Simulated migration crash table corrupt'));

        $this->app->instance(TenantMigrationService::class, $failingMigrationService);

        /** @var TenantProvisioningService $provisioningService */
        $provisioningService = $this->app->make(TenantProvisioningService::class);

        $exceptionThrown = false;
        try {
            $provisioningService->provision(
                tenant: $tenant,
                provisioningJob: $job,
                attempt: 1,
            );
        } catch (TenantProvisioningException $e) {
            $exceptionThrown = true;
            $this->assertStringContainsString('Simulated migration crash table corrupt', $e->getMessage());
        }

        $this->assertTrue($exceptionThrown, 'TenantProvisioningException should have been thrown.');

        // Invariants Verification on Failure
        $tenant->refresh();
        $database = $tenant->database->fresh();
        $job->refresh();

        // 1. Tenant status stays/returns to PENDING
        $this->assertSame(TenantStatus::PENDING, $tenant->status);

        // 2. Database status marked as FAILED
        $this->assertSame(TenantDatabaseStatus::FAILED, $database->status);

        // 3. Provisioning job marked as FAILED with error message
        $this->assertSame(ProvisioningJobStatus::FAILED, $job->status);
        $this->assertStringContainsString('Simulated migration crash table corrupt', $job->error_message);
        $this->assertNotNull($job->completed_at);

        // 4. Physical database is PRESERVED for diagnosis (not dropped/deleted)
        $this->assertFileExists($this->tempDbPath);

        // 5. Concurrency lock is RELEASED in finally
        /** @var TenantProvisioningLock $lock */
        $lock = $this->app->make(TenantProvisioningLock::class);
        $this->assertFalse($lock->isLocked((string) $tenant->getKey()));

        // 6. TenantProvisioningFailed event dispatched
        Event::assertDispatched(TenantProvisioningFailed::class, function (TenantProvisioningFailed $event) use ($tenant) {
            return $event->tenantId === (string) $tenant->getKey();
        });
    }
}
