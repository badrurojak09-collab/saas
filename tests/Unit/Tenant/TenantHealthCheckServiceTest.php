<?php

namespace Tests\Unit\Tenant;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Models\Landlord\TenantDatabase;
use App\Services\Tenant\TenantHealthCheckService;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Exceptions\Provisioning\TenantHealthCheckException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenantHealthCheckServiceTest extends TestCase
{
    private TenantConnectionManager $connectionManager;

    private TenantHealthCheckService $healthCheckService;

    private string $testDbPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connectionManager = new TenantConnectionManager();
        $this->healthCheckService = new TenantHealthCheckService($this->connectionManager);
        $this->testDbPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_health_'.uniqid().'.sqlite';
        touch($this->testDbPath);
    }

    protected function tearDown(): void
    {
        $this->connectionManager->disconnect();
        if (file_exists($this->testDbPath)) {
            @unlink($this->testDbPath);
        }
        parent::tearDown();
    }

    public function test_health_check_fails_when_critical_tables_are_missing(): void
    {
        $db = new TenantDatabase([
            'database' => $this->testDbPath,
            'driver' => 'sqlite',
            'status' => TenantDatabaseStatus::READY,
        ]);

        $result = $this->healthCheckService->check($db);

        $this->assertFalse($result['healthy']);
        $this->assertTrue($result['checks']['connectivity']);
        $this->assertFalse($result['checks']['critical_tables']);
        $this->assertStringContainsString('Critical tables missing', (string) $result['error']);
    }

    public function test_health_check_passes_when_critical_tables_exist(): void
    {
        $db = new TenantDatabase([
            'database' => $this->testDbPath,
            'driver' => 'sqlite',
            'status' => TenantDatabaseStatus::READY,
        ]);

        // Create critical tables in SQLite test db
        $this->connectionManager->configureForProvisioning($db);
        $conn = $this->connectionManager->getConnectionName();

        foreach (TenantHealthCheckService::CRITICAL_TABLES as $table) {
            Schema::connection($conn)->create($table, function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->timestamps();
            });
        }

        $result = $this->healthCheckService->check($db);

        $this->assertTrue($result['healthy']);
        $this->assertTrue($result['checks']['connectivity']);
        $this->assertTrue($result['checks']['critical_tables']);
        $this->assertNull($result['error']);
    }

    public function test_assert_healthy_throws_exception_on_failure(): void
    {
        $db = new TenantDatabase([
            'database' => $this->testDbPath,
            'driver' => 'sqlite',
            'status' => TenantDatabaseStatus::READY,
        ]);

        $this->expectException(TenantHealthCheckException::class);
        $this->healthCheckService->assertHealthy($db);
    }
}
