<?php

namespace Tests\Integration\Provisioning;

use App\Actions\Landlord\CreateTenantAction;
use App\DTOs\Tenant\ProvisionTenantData;
use App\DTOs\Tenant\TenantAdminData;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Events\Landlord\TenantCreated;
use App\Events\Landlord\TenantProvisioningCompleted;
use App\Events\Landlord\TenantProvisioningStarted;
use App\Models\Landlord\Domain;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Models\Landlord\TenantMigrationVersion;
use App\Models\Landlord\TenantSubscription;
use App\Models\Tenant\User;
use App\Services\Tenant\TenantProvisioningService;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;
use App\Tenancy\Contracts\TenantManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenantProvisioningIntegrationTest extends TestCase
{
    private TenantProvisioningService $provisioningService;

    private TenantConnectionManager $connectionManager;

    private TenantContext $tenantContext;

    private string $tempDbPath;

    private function cleanupTestTenants(): void
    {
        $tenants = Tenant::query()->where('code', 'like', 'UIH%')->get();
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
        $this->provisioningService = app(TenantProvisioningService::class);
        $this->connectionManager = app(TenantConnectionManager::class);
        $this->tenantContext = app(TenantContext::class);
        $this->tempDbPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_tenant_e2e_'.uniqid().'.sqlite';
        $this->cleanupTestTenants();
    }

    protected function tearDown(): void
    {
        $this->connectionManager->disconnect();
        $this->tenantContext->clear();
        $this->cleanupTestTenants();
        if (file_exists($this->tempDbPath)) {
            @unlink($this->tempDbPath);
        }
        parent::tearDown();
    }

    public function test_full_provisioning_lifecycle_e2e(): void
    {
        Event::fake([
            TenantCreated::class,
            TenantProvisioningStarted::class,
            TenantProvisioningCompleted::class,
        ]);

        $suffix = strtoupper(substr(uniqid(), -4));
        $code = 'UIH_'.$suffix;
        $slug = 'uih-'.strtolower($suffix);

        // 1. Create Tenant & Metadata via Action
        $createAction = new CreateTenantAction;
        $dto = new ProvisionTenantData(
            name: 'Universitas Indonesia Hebat',
            code: $code,
            slug: $slug,
            database: $this->tempDbPath,
            driver: 'sqlite',
            admin: new TenantAdminData(
                name: 'Administrator UIH',
                email: 'admin@uih.ac.id',
                username: 'admin_uih',
                password: 'SecretPassword123!',
            ),
        );

        $tenant = $createAction->execute($dto);

        $this->assertSame(TenantStatus::PENDING, $tenant->status);
        $this->assertSame(TenantDatabaseStatus::PROVISIONING, $tenant->database->status);

        // 2. Create Provisioning Job Record
        /** @var ProvisioningJob $job */
        $job = ProvisioningJob::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 1,
        ]);

        // 3. Run Provisioning Engine
        $this->provisioningService->provision(
            tenant: $tenant,
            provisioningJob: $job,
            adminData: $dto->admin,
            attempt: 1,
        );

        // 4. Invariants Verification
        $tenant->refresh();
        $database = $tenant->database->fresh();
        $job->refresh();

        // Invariant #1: ACTIVE = READY = HEALTHY
        $this->assertSame(TenantStatus::ACTIVE, $tenant->status);
        $this->assertSame(TenantDatabaseStatus::READY, $database->status);
        $this->assertSame(ProvisioningJobStatus::COMPLETED, $job->status);
        $this->assertNotNull($database->last_migrated_at);
        $this->assertNotNull($job->completed_at);

        // 5. Verification of Migrations on Tenant DB
        $this->connectionManager->configure($database);
        $this->assertTrue(Schema::connection('tenant')->hasTable('users'));
        $this->assertTrue(Schema::connection('tenant')->hasTable('faculties'));
        $this->assertTrue(Schema::connection('tenant')->hasTable('roles'));
        $this->assertTrue(Schema::connection('tenant')->hasTable('permissions'));

        // 6. Verification of Migration Versions in Landlord DB
        $versionCount = TenantMigrationVersion::query()
            ->where('tenant_database_id', $database->getKey())
            ->count();
        $this->assertGreaterThan(10, $versionCount);

        // 7. Verification of Admin Account Creation & Role
        app(TenantManager::class)->initialize($tenant);
        /** @var User|null $admin */
        $admin = User::query()->where('email', 'admin@uih.ac.id')->first();
        $this->assertNotNull($admin);
        $this->assertSame('Administrator UIH', $admin->name);
        $this->assertSame('admin_uih', $admin->username);
        $this->assertTrue($admin->hasRole('super_admin'));

        // 8. Events Verification
        Event::assertDispatched(TenantProvisioningStarted::class);
        Event::assertDispatched(TenantProvisioningCompleted::class);
    }
}
