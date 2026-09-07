<?php

namespace Tests\Unit\Tenant;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Models\Landlord\TenantDatabase;
use App\Services\Tenant\TenantDatabaseService;
use App\Tenancy\Exceptions\Provisioning\TenantDatabaseCreationException;
use Tests\TestCase;

class TenantDatabaseServiceTest extends TestCase
{
    private TenantDatabaseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TenantDatabaseService();
    }

    public function test_validates_complete_configuration(): void
    {
        $db = new TenantDatabase([
            'database' => 'siakad_tenant_test',
            'host' => '127.0.0.1',
            'port' => 3306,
        ]);

        $this->assertTrue($this->service->validateConfiguration($db));
    }

    public function test_validates_incomplete_configuration(): void
    {
        $db = new TenantDatabase([
            'database' => '',
            'host' => '127.0.0.1',
            'port' => 0,
        ]);

        $this->assertFalse($this->service->validateConfiguration($db));
    }

    public function test_creates_physical_database_sqlite_idempotently(): void
    {
        $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_test_provision_'.uniqid().'.sqlite';

        $db = new TenantDatabase([
            'database' => $tempPath,
            'driver' => 'sqlite',
            'host' => '127.0.0.1',
            'port' => 3306,
            'status' => TenantDatabaseStatus::PROVISIONING,
        ]);

        try {
            // First creation
            $this->service->createPhysicalDatabase($db);
            $this->assertFileExists($tempPath);

            // Second creation (idempotency check)
            $this->service->createPhysicalDatabase($db);
            $this->assertFileExists($tempPath);
        } finally {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    public function test_throws_exception_if_database_name_is_empty(): void
    {
        $db = new TenantDatabase([
            'database' => '',
            'driver' => 'sqlite',
        ]);

        $this->expectException(TenantDatabaseCreationException::class);
        $this->service->createPhysicalDatabase($db);
    }

    public function test_test_connection_fails_if_database_not_ready(): void
    {
        $db = new TenantDatabase([
            'database' => 'siakad_test',
            'driver' => 'sqlite',
            'status' => TenantDatabaseStatus::PROVISIONING,
        ]);

        $result = $this->service->testConnection($db);

        $this->assertSame('failed', $result['status']);
        $this->assertFalse($result['connected']);
        $this->assertStringContainsString('Tenant database status is [provisioning]', $result['error']);
    }
}
