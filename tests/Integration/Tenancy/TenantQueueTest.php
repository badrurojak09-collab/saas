<?php

namespace Tests\Integration\Tenancy;

use App\Models\Landlord\Tenant;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use App\Tenancy\Exceptions\TenantContextMissingException;
use App\Tenancy\Queue\InitializeTenantForJob;
use App\Tenancy\Queue\TenantAwareJob;
use Exception;
use Tests\TestCase;

class TenantQueueTest extends TestCase
{
    public function test_job_missing_tenant_id_throws_exception(): void
    {
        $job = new class {
            public string $tenantId = '';
        };

        $middleware = new InitializeTenantForJob();

        $this->expectException(TenantContextMissingException::class);
        $this->expectExceptionMessage('requires a valid tenantId property');

        $middleware->handle($job, fn () => null);
    }

    public function test_queue_middleware_initializes_and_cleans_up_tenant(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000001';
        $tenant->code = 'INST-001';

        $resolver = $this->createMock(TenantResolver::class);
        $resolver->expects($this->once())
            ->method('resolveFromId')
            ->with('INST-001')
            ->willReturn($tenant);

        $manager = $this->createMock(TenantManager::class);
        $manager->expects($this->once())
            ->method('initialize')
            ->with($tenant);
        $manager->expects($this->once())
            ->method('end');

        $this->app->instance(TenantResolver::class, $resolver);
        $this->app->instance(TenantManager::class, $manager);

        $job = new class {
            use TenantAwareJob;
        };
        $job->forTenant('INST-001');

        $middleware = new InitializeTenantForJob();

        $executed = false;
        $middleware->handle($job, function () use (&$executed) {
            $executed = true;
        });

        $this->assertTrue($executed);
    }

    public function test_queue_middleware_cleans_up_even_on_job_failure(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000001';

        $resolver = $this->createMock(TenantResolver::class);
        $resolver->method('resolveFromId')->willReturn($tenant);

        $manager = $this->createMock(TenantManager::class);
        $manager->expects($this->once())->method('initialize');
        // Crucial invariant: end() MUST still be called when job throws exception!
        $manager->expects($this->once())->method('end');

        $this->app->instance(TenantResolver::class, $resolver);
        $this->app->instance(TenantManager::class, $manager);

        $job = new class {
            use TenantAwareJob;
        };
        $job->forTenant('01900000-0000-7000-8000-000000000001');

        $middleware = new InitializeTenantForJob();

        try {
            $middleware->handle($job, function () {
                throw new Exception('Something went wrong during job execution!');
            });
            $this->fail('Expected exception was not thrown.');
        } catch (Exception $e) {
            $this->assertSame('Something went wrong during job execution!', $e->getMessage());
        }
    }
}
