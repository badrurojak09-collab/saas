<?php

namespace App\Services\Tenant;

use App\Models\Landlord\TenantDatabase;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Exceptions\Provisioning\TenantHealthCheckException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TenantHealthCheckService
{
    /**
     * Critical tables required for tenant operational readiness.
     *
     * @var array<int, string>
     */
    public const CRITICAL_TABLES = [
        'users',
        'roles',
        'permissions',
        'faculties',
    ];

    public function __construct(
        protected TenantConnectionManager $connectionManager,
    ) {}

    /**
     * Run a multi-level health check on the tenant database.
     *
     * @return array{healthy: bool, latency_ms: float, checks: array<string, bool>, error: ?string}
     */
    public function check(TenantDatabase $database): array
    {
        $start = microtime(true);
        $checks = [
            'connectivity' => false,
            'critical_tables' => false,
        ];

        try {
            $this->connectionManager->configureForProvisioning($database);
            $connection = DB::connection($this->connectionManager->getConnectionName());

            // Level 1: Connectivity probe (SELECT 1)
            $connection->select('SELECT 1');
            $checks['connectivity'] = true;

            // Level 2: Schema verification (critical tables)
            $missingTables = [];
            foreach (self::CRITICAL_TABLES as $table) {
                if (! Schema::connection($this->connectionManager->getConnectionName())->hasTable($table)) {
                    $missingTables[] = $table;
                }
            }

            if (! empty($missingTables)) {
                $latency = round((microtime(true) - $start) * 1000, 2);

                return [
                    'healthy' => false,
                    'latency_ms' => $latency,
                    'checks' => $checks,
                    'error' => sprintf('Critical tables missing: %s', implode(', ', $missingTables)),
                ];
            }

            $checks['critical_tables'] = true;
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'healthy' => true,
                'latency_ms' => $latency,
                'checks' => $checks,
                'error' => null,
            ];
        } catch (Throwable $e) {
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'healthy' => false,
                'latency_ms' => $latency,
                'checks' => $checks,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Assert that tenant database passes health check, or throw TenantHealthCheckException.
     *
     * @throws TenantHealthCheckException
     */
    public function assertHealthy(TenantDatabase $database): void
    {
        $result = $this->check($database);

        if (! $result['healthy']) {
            throw new TenantHealthCheckException(
                sprintf('Tenant health check failed for [%s]: %s', $database->database, $result['error'])
            );
        }
    }
}
