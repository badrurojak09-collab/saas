<?php

namespace Tests\Integration\Provisioning;

use App\Actions\Landlord\CreateTenantAction;
use App\DTOs\Tenant\ProvisionTenantData;
use App\DTOs\Tenant\TenantAdminData;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Events\Landlord\TenantCreated;
use App\Events\Landlord\TenantProvisioningCompleted;
use App\Events\Landlord\TenantProvisioningStarted;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\Faculty;
use App\Services\Tenant\TenantProvisioningService;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Exceptions\TenantContextMissingException;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TenantIsolationPostProvisionTest extends TestCase
{
    private string $tempDbPathA;

    private string $tempDbPathB;

    private function cleanupTestTenants(): void
    {
        $tenants = Tenant::query()->where('code', 'like', 'ISO_%')->get();
        foreach ($tenants as $t) {
            $t->domains()->forceDelete();
            $t->database()->forceDelete();
            $t->provisioningJobs()->forceDelete();
            $t->subscriptions()->forceDelete();
            $t->forceDelete();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDbPathA = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_tenant_iso_a_'.uniqid().'.sqlite';
        $this->tempDbPathB = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_tenant_iso_b_'.uniqid().'.sqlite';
        $this->cleanupTestTenants();
    }

    protected function tearDown(): void
    {
        app(TenantManager::class)->end();
        $this->cleanupTestTenants();
        if (file_exists($this->tempDbPathA)) {
            @unlink($this->tempDbPathA);
        }
        if (file_exists($this->tempDbPathB)) {
            @unlink($this->tempDbPathB);
        }
        parent::tearDown();
    }

    public function test_post_provisioning_cross_tenant_database_isolation(): void
    {
        Event::fake([
            TenantCreated::class,
            TenantProvisioningStarted::class,
            TenantProvisioningCompleted::class,
        ]);

        $action = new CreateTenantAction;
        /** @var TenantProvisioningService $service */
        $service = app(TenantProvisioningService::class);
        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);

        // 1. Provision Tenant A
        $suffixA = strtoupper(substr(uniqid(), -4));
        $tenantA = $action->execute(new ProvisionTenantData(
            name: 'Universitas A Alpha',
            code: 'ISO_A_'.$suffixA,
            slug: 'iso-a-'.strtolower($suffixA),
            database: $this->tempDbPathA,
            driver: 'sqlite',
            admin: new TenantAdminData(
                name: 'Admin Alpha',
                email: 'admin@alpha.ac.id',
                username: 'admin_alpha',
                password: 'SecretPassword123!',
            ),
        ));

        $jobA = ProvisioningJob::query()->create([
            'tenant_id' => $tenantA->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 1,
        ]);
        $service->provision($tenantA, $jobA, attempt: 1);

        // 2. Provision Tenant B
        $suffixB = strtoupper(substr(uniqid(), -4));
        $tenantB = $action->execute(new ProvisionTenantData(
            name: 'Universitas B Beta',
            code: 'ISO_B_'.$suffixB,
            slug: 'iso-b-'.strtolower($suffixB),
            database: $this->tempDbPathB,
            driver: 'sqlite',
            admin: new TenantAdminData(
                name: 'Admin Beta',
                email: 'admin@beta.ac.id',
                username: 'admin_beta',
                password: 'SecretPassword123!',
            ),
        ));

        $jobB = ProvisioningJob::query()->create([
            'tenant_id' => $tenantB->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 1,
        ]);
        $service->provision($tenantB, $jobB, attempt: 1);

        // 3. Enter Context Tenant A and create a unique record
        $manager->initialize($tenantA);
        $this->assertSame($tenantA->getKey(), $manager->current()?->getKey());

        Faculty::create([
            'code' => 'FK_ALPHA',
            'name' => 'Fakultas Kedokteran Alpha',
            'short_name' => 'FK-A',
            'status' => 'active',
        ]);

        $this->assertNotNull(Faculty::where('code', 'FK_ALPHA')->first());

        // 4. Switch Context to Tenant B
        $manager->end();
        $manager->initialize($tenantB);
        $this->assertSame($tenantB->getKey(), $manager->current()?->getKey());

        // FK_ALPHA must NOT exist in Tenant B database
        $this->assertNull(Faculty::where('code', 'FK_ALPHA')->first());

        // Create a unique record in Tenant B
        Faculty::create([
            'code' => 'FT_BETA',
            'name' => 'Fakultas Teknik Beta',
            'short_name' => 'FT-B',
            'status' => 'active',
        ]);

        $this->assertNotNull(Faculty::where('code', 'FT_BETA')->first());

        // 5. Switch back to Tenant A
        $manager->end();
        $manager->initialize($tenantA);
        $this->assertNotNull(Faculty::where('code', 'FK_ALPHA')->first());
        $this->assertNull(Faculty::where('code', 'FT_BETA')->first());

        // 6. End Tenancy Context -> Strict mode throws exception
        $manager->end();
        $this->assertFalse($manager->isInitialized());

        $this->expectException(TenantContextMissingException::class);
        Faculty::where('code', 'FK_ALPHA')->first();
    }
}
