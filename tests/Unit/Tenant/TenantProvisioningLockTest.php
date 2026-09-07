<?php

namespace Tests\Unit\Tenant;

use App\Tenancy\Exceptions\Provisioning\TenantProvisioningAlreadyRunningException;
use App\Tenancy\Provisioning\TenantProvisioningLock;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TenantProvisioningLockTest extends TestCase
{
    private TenantProvisioningLock $lockService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lockService = new TenantProvisioningLock();
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    public function test_acquires_and_releases_lock(): void
    {
        $tenantId = '01915647-89ab-7def-8123-456789abcdef';

        $lock = $this->lockService->acquire($tenantId, 60);
        $this->assertNotNull($lock);

        $this->lockService->release($tenantId);
    }

    public function test_acquiring_duplicate_lock_throws_exception(): void
    {
        $tenantId = '01915647-89ab-7def-8123-456789abcdef';

        $this->lockService->acquire($tenantId, 60);

        $this->expectException(TenantProvisioningAlreadyRunningException::class);
        $this->expectExceptionMessage(sprintf('Tenant [%s] is already being provisioned.', $tenantId));

        $secondService = new TenantProvisioningLock();
        $secondService->acquire($tenantId, 60);
    }

    public function test_run_executes_callback_and_releases_lock(): void
    {
        $tenantId = '01915647-89ab-7def-8123-456789abcdef';

        $executed = false;
        $result = $this->lockService->run($tenantId, function () use (&$executed) {
            $executed = true;

            return 'completed';
        });

        $this->assertTrue($executed);
        $this->assertSame('completed', $result);

        // Lock must be released, so another acquire succeeds
        $secondLock = $this->lockService->acquire($tenantId, 60);
        $this->assertNotNull($secondLock);
        $this->lockService->release($tenantId);
    }
}
