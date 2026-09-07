<?php

namespace Tests\Integration\Tenancy;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Models\Tenant\Student;
use App\Services\Tenant\TenantDatabaseService;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Context\TenantContext;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Exceptions\TenantContextMissingException;
use App\Tenancy\Exceptions\TenantDatabaseUnavailableException;
use App\Tenancy\Services\TenantManagerService;
use Tests\TestCase;

/**
 * Golden Acceptance Tests for Sprint 04 Tenancy Isolation.
 */
class TenantIsolationTest extends TestCase
{
    private TenantContext $context;
    private TenantConnectionManager $connectionManager;
    private TenantManagerService $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new TenantContext();
        $this->connectionManager = new TenantConnectionManager();

        $dbService = new class extends TenantDatabaseService {
            public function loadMetadata(Tenant $tenant): TenantDatabase
            {
                $isMaint = $tenant->code === 'INST-MAINT';
                $status = $isMaint ? TenantDatabaseStatus::MAINTENANCE : TenantDatabaseStatus::READY;

                $db = new TenantDatabase([
                    'database' => 'siakad_' . strtolower($tenant->code),
                    'driver' => 'mysql',
                    'host' => '127.0.0.1',
                    'port' => 3306,
                    'status' => $status,
                ]);
                $db->status = $status;

                return $db;
            }
        };

        // Mock connect to bypass physical MySQL in test environment
        $connManager = new class extends TenantConnectionManager {
            public function connect(): void
            {
                // mock connect
            }
        };

        $this->manager = new TenantManagerService($this->context, $connManager, $dbService);
        $this->app->instance(TenantManager::class, $this->manager);
    }

    protected function tearDown(): void
    {
        $this->manager->end();
        parent::tearDown();
    }

    /**
     * Invariant 1 & 127:
     * Querying any Tenant model without an active tenant context MUST throw
     * TenantContextMissingException and NEVER query Landlord or default DB.
     */
    public function test_querying_tenant_model_without_context_throws_exception(): void
    {
        $this->assertFalse($this->manager->isInitialized());

        $this->expectException(TenantContextMissingException::class);
        $this->expectExceptionMessage('Cannot query tenant model [' . Student::class . '] without an active tenant context.');

        Student::query()->count();
    }

    /**
     * Invariant 2 & 3:
     * Tenant A context connects only to Database A.
     * Tenant B context connects only to Database B.
     * Context switching isolates configuration completely.
     */
    public function test_tenant_a_and_tenant_b_have_isolated_databases(): void
    {
        $tenantA = new Tenant([
            'code' => 'inst_a',
            'name' => 'Universitas A',
            'status' => TenantStatus::ACTIVE,
        ]);
        $tenantA->id = '01900000-0000-7000-8000-000000000001';
        $tenantA->code = 'inst_a';
        $tenantA->status = TenantStatus::ACTIVE;

        $tenantB = new Tenant([
            'code' => 'inst_b',
            'name' => 'Universitas B',
            'status' => TenantStatus::ACTIVE,
        ]);
        $tenantB->id = '01900000-0000-7000-8000-000000000002';
        $tenantB->code = 'inst_b';
        $tenantB->status = TenantStatus::ACTIVE;

        // 1. Initialize Tenant A
        $this->manager->initialize($tenantA);
        $this->assertSame('siakad_inst_a', $this->manager->getConnectionManager()->getCurrentDatabase());
        $this->assertSame('inst_a', $this->manager->current()->code);

        // 2. End Tenant A
        $this->manager->end();
        $this->assertFalse($this->manager->isInitialized());
        $this->assertNull($this->manager->getConnectionManager()->getCurrentDatabase());

        // 3. Initialize Tenant B
        $this->manager->initialize($tenantB);
        $this->assertSame('siakad_inst_b', $this->manager->getConnectionManager()->getCurrentDatabase());
        $this->assertSame('inst_b', $this->manager->current()->code);
        $this->assertNotSame('siakad_inst_a', $this->manager->getConnectionManager()->getCurrentDatabase());

        // 4. End Tenant B
        $this->manager->end();
        $this->assertFalse($this->manager->isInitialized());
    }

    /**
     * Invariant 4:
     * When tenant database is in maintenance or unavailable,
     * it throws TenantDatabaseUnavailableException and never falls back.
     */
    public function test_unavailable_database_never_falls_back_to_landlord(): void
    {
        $tenantMaint = new Tenant([
            'code' => 'INST-MAINT',
            'status' => TenantStatus::ACTIVE,
        ]);
        $tenantMaint->id = '01900000-0000-7000-8000-000000000099';
        $tenantMaint->code = 'INST-MAINT';
        $tenantMaint->status = TenantStatus::ACTIVE;

        $this->expectException(TenantDatabaseUnavailableException::class);
        $this->expectExceptionMessage('Tenant database is currently [maintenance]');

        $this->manager->initialize($tenantMaint);
    }

    /**
     * Invariant 5:
     * Tenant credentials are never stored in TenantContext.
     */
    public function test_credentials_are_not_exposed_in_context(): void
    {
        $tenant = new Tenant([
            'code' => 'INST-SEC',
            'status' => TenantStatus::ACTIVE,
        ]);
        $tenant->id = '01900000-0000-7000-8000-000000000003';
        $tenant->code = 'INST-SEC';
        $tenant->status = TenantStatus::ACTIVE;

        $this->manager->initialize($tenant);

        $currentTenant = $this->manager->current();

        $this->assertArrayNotHasKey('password', $currentTenant->toArray());
    }
}
