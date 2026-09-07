<?php

namespace App\Services\Tenant;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Tenancy\Exceptions\Provisioning\TenantDatabaseCreationException;
use App\Tenancy\Exceptions\TenantDatabaseNotConfiguredException;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class TenantDatabaseService
{
    /**
     * Load the primary database metadata for a tenant.
     */
    public function loadMetadata(Tenant $tenant): TenantDatabase
    {
        /** @var TenantDatabase|null $database */
        $database = $tenant->relationLoaded('database')
            ? $tenant->database
            : TenantDatabase::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('is_primary', true)
                ->first();

        if (! $database) {
            // Fallback to any database record if is_primary wasn't explicitly flagged
            $database = TenantDatabase::query()
                ->where('tenant_id', $tenant->getKey())
                ->first();
        }

        if (! $database) {
            throw new TenantDatabaseNotConfiguredException(
                "Tenant [{$tenant->code}] does not have an associated database configured."
            );
        }

        return $database;
    }

    /**
     * Validate database configuration completeness.
     */
    public function validateConfiguration(TenantDatabase $database): bool
    {
        return ! empty($database->database) &&
            ! empty($database->host) &&
            $database->port > 0;
    }

    /**
     * Physically create the tenant database (MySQL/SQLite) idempotently.
     *
     * @throws TenantDatabaseCreationException
     */
    public function createPhysicalDatabase(TenantDatabase $database): void
    {
        if (empty($database->database)) {
            throw new TenantDatabaseCreationException(
                'Cannot create physical database: database name is empty.'
            );
        }

        try {
            $driver = $database->driver ?: (str_ends_with($database->database, '.sqlite') ? 'sqlite' : config('database.default', 'mysql'));

            if ($driver === 'sqlite' || str_ends_with($database->database, '.sqlite') || str_contains($database->database, DIRECTORY_SEPARATOR)) {
                if ($database->database !== ':memory:') {
                    $path = $database->database;
                    $directory = dirname($path);

                    if (! file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }

                    if (! file_exists($path)) {
                        touch($path);
                    }
                }

                return;
            }

            $identifier = str_replace('`', '``', $database->database);
            DB::connection('landlord')->statement(
                "CREATE DATABASE IF NOT EXISTS `{$identifier}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        } catch (Throwable $e) {
            throw new TenantDatabaseCreationException(
                sprintf('Failed to create physical database [%s]: %s', $database->database, $e->getMessage()),
                $e
            );
        }
    }

    /**
     * Test connection to a tenant database directly via a temporary PDO connection.
     * Measures latency in milliseconds.
     */
    public function testConnection(TenantDatabase $database): array
    {
        $status = $database->status instanceof TenantDatabaseStatus
            ? $database->status
            : TenantDatabaseStatus::tryFrom((string) $database->status);

        if ($status !== TenantDatabaseStatus::READY) {
            $statusVal = $database->status instanceof TenantDatabaseStatus ? $database->status->value : (string) $database->status;

            return [
                'status' => 'failed',
                'connected' => false,
                'latency_ms' => 0,
                'error' => "Tenant database status is [{$statusVal}].",
            ];
        }

        $start = microtime(true);

        try {
            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s;charset=%s',
                $database->driver ?: 'mysql',
                $database->host ?: '127.0.0.1',
                (int) ($database->port ?: 3306),
                $database->database,
                'utf8mb4',
            );

            $pdo = new PDO(
                $dsn,
                $database->username ?: 'root',
                $database->password,
                [
                    PDO::ATTR_TIMEOUT => 3,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );

            $stmt = $pdo->query('SELECT 1');
            $stmt->fetch();

            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'success',
                'connected' => true,
                'latency_ms' => $latency,
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'status' => 'failed',
                'connected' => false,
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
                'error' => 'Connection failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Build connection array configuration from TenantDatabase model.
     */
    public function getConnectionConfiguration(TenantDatabase $database): array
    {
        return [
            'driver' => $database->driver ?: 'mysql',
            'host' => $database->host ?: '127.0.0.1',
            'port' => (int) ($database->port ?: 3306),
            'database' => $database->database,
            'username' => $database->username ?: 'root',
            'password' => $database->password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ];
    }

    /**
     * Run a health check for a tenant's database.
     */
    public function healthCheck(Tenant $tenant): array
    {
        try {
            $database = $this->loadMetadata($tenant);
            $test = $this->testConnection($database);

            return [
                'tenant' => $tenant->code,
                'database' => $database->database,
                'status' => $test['connected'] ? 'healthy' : 'unhealthy',
                'latency_ms' => $test['latency_ms'],
                'error' => $test['error'],
            ];
        } catch (Throwable $e) {
            return [
                'tenant' => $tenant->code,
                'database' => null,
                'status' => 'unhealthy',
                'latency_ms' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }
}
