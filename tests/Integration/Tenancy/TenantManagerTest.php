<?php

namespace Tests\Integration\Tenancy;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Services\Tenant\TenantDatabaseService;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;
use App\Tenancy\Exceptions\TenantConnectionException;
use App\Tenancy\Exceptions\TenantInactiveException;
use App\Tenancy\Services\TenantManagerService;
use Tests\TestCase;

class TenantManagerTest extends TestCase
{
    private TenantContext $context;
    private TenantConnectionManager $connectionManager;
    private TenantManagerService $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new TenantContext();
        $this->connectionManager = new TenantConnectionManager();

        // Mock/dummy database service
        $dbService = new class extends TenantDatabaseService {
            public function loadMetadata(Tenant $tenant): TenantDatabase
            {
                $db = new TenantDatabase([
                    'database' => 'siakad_' . strtolower($tenant->code),
                    'driver' => 'sqlite',
                    'host' => '127.0.0.1',
                    'port' => 3306,
                    'status' => TenantDatabaseStatus::READY,
                ]);
                $db->status = TenantDatabaseStatus::READY;
                return $db;
            }
        };

        // Create connection manager that doesn't actually connect to remote MySQL in unit test
        $connManager = new class extends TenantConnectionManager {
            public function connect(): void
            {
                // mock connect
            }
        };

        $this->manager = new TenantManagerService($this->context, $connManager, $dbService);
    }

    protected function tearDown(): void
    {
        $this->manager->end();
        parent::tearDown();
    }

    public function test_manager_lifecycle(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000001';
        $tenant->code = 'INST-001';
        $tenant->status = TenantStatus::ACTIVE;

        $this->assertFalse($this->manager->isInitialized());
        $this->assertNull($this->manager->current());

        $this->manager->initialize($tenant);

        $this->assertTrue($this->manager->isInitialized());
        $this->assertSame($tenant, $this->manager->current());
        $this->assertSame($tenant, $this->manager->requireCurrent());

        $this->manager->end();

        $this->assertFalse($this->manager->isInitialized());
        $this->assertNull($this->manager->current());
    }

    public function test_cannot_initialize_inactive_tenant(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000002';
        $tenant->code = 'INST-SUSP';
        $tenant->status = TenantStatus::SUSPENDED;

        $this->expectException(TenantInactiveException::class);

        $this->manager->initialize($tenant);
    }

    public function test_double_initialize_with_same_tenant_is_idempotent(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000001';
        $tenant->code = 'INST-001';
        $tenant->status = TenantStatus::ACTIVE;

        $this->manager->initialize($tenant);
        $this->manager->initialize($tenant);

        $this->assertTrue($this->manager->isInitialized());
        $this->assertSame($tenant, $this->manager->current());
    }

    public function test_double_initialize_with_different_tenant_throws_exception(): void
    {
        $tenantA = new Tenant();
        $tenantA->id = '01900000-0000-7000-8000-000000000001';
        $tenantA->code = 'INST-001';
        $tenantA->status = TenantStatus::ACTIVE;

        $tenantB = new Tenant();
        $tenantB->id = '01900000-0000-7000-8000-000000000002';
        $tenantB->code = 'INST-002';
        $tenantB->status = TenantStatus::ACTIVE;

        $this->manager->initialize($tenantA);

        $this->expectException(TenantConnectionException::class);
        $this->expectExceptionMessage('Tenant context is already initialized with [INST-001]');

        $this->manager->initialize($tenantB);
    }
}
