<?php

namespace Tests\Integration\Tenancy;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Models\Landlord\TenantDatabase;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Exceptions\TenantConnectionException;
use App\Tenancy\Exceptions\TenantDatabaseUnavailableException;
use Tests\TestCase;

class TenantConnectionTest extends TestCase
{
    private TenantConnectionManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TenantConnectionManager();
    }

    protected function tearDown(): void
    {
        $this->manager->disconnect();
        parent::tearDown();
    }

    public function test_tenant_database_can_be_configured_dynamically(): void
    {
        $database = new TenantDatabase([
            'database' => 'siakad_tenant_a',
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'username' => 'root',
            'status' => TenantDatabaseStatus::READY,
        ]);
        $database->status = TenantDatabaseStatus::READY;

        $this->manager->configure($database);

        $this->assertSame('siakad_tenant_a', $this->manager->getCurrentDatabase());
        $this->assertSame('siakad_tenant_a', config('database.connections.tenant.database'));
        $this->assertSame('127.0.0.1', config('database.connections.tenant.host'));
        $this->assertSame(3306, config('database.connections.tenant.port'));
    }

    public function test_switching_database_replaces_old_configuration(): void
    {
        $dbA = new TenantDatabase(['database' => 'siakad_tenant_a']);
        $dbA->status = TenantDatabaseStatus::READY;

        $dbB = new TenantDatabase(['database' => 'siakad_tenant_b']);
        $dbB->status = TenantDatabaseStatus::READY;

        $this->manager->configure($dbA);
        $this->assertSame('siakad_tenant_a', $this->manager->getCurrentDatabase());

        $this->manager->configure($dbB);
        $this->assertSame('siakad_tenant_b', $this->manager->getCurrentDatabase());
    }

    public function test_disconnect_clears_database_configuration(): void
    {
        $db = new TenantDatabase(['database' => 'siakad_tenant_a']);
        $db->status = TenantDatabaseStatus::READY;

        $this->manager->configure($db);
        $this->assertSame('siakad_tenant_a', $this->manager->getCurrentDatabase());

        $this->manager->disconnect();
        $this->assertNull($this->manager->getCurrentDatabase());
    }

    public function test_empty_database_name_throws_connection_exception(): void
    {
        $db = new TenantDatabase(['database' => '']);
        $db->status = TenantDatabaseStatus::READY;

        $this->expectException(TenantConnectionException::class);
        $this->expectExceptionMessage('Tenant database name is empty');

        $this->manager->configure($db);
    }

    public function test_maintenance_database_status_throws_unavailable_exception(): void
    {
        $db = new TenantDatabase(['database' => 'siakad_tenant_maint']);
        $db->status = TenantDatabaseStatus::MAINTENANCE;

        $this->expectException(TenantDatabaseUnavailableException::class);
        $this->expectExceptionMessage('Tenant database is currently [maintenance]');

        $this->manager->configure($db);
    }

    public function test_pending_database_status_throws_unavailable_exception(): void
    {
        $db = new TenantDatabase(['database' => 'siakad_tenant_pending']);
        $db->status = TenantDatabaseStatus::PROVISIONING;

        $this->expectException(TenantDatabaseUnavailableException::class);
        $this->expectExceptionMessage('Tenant database is currently [provisioning]');

        $this->manager->configure($db);
    }
}
