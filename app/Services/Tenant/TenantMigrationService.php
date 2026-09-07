<?php

namespace App\Services\Tenant;

use App\Models\Landlord\TenantDatabase;
use App\Models\Landlord\TenantMigrationVersion;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Exceptions\Provisioning\TenantMigrationException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TenantMigrationService
{
    public const DEFAULT_MIGRATION_PATH = 'database/migrations/tenant';

    public function __construct(
        protected TenantConnectionManager $connectionManager,
    ) {}

    /**
     * Run tenant database migrations and record versions into landlord table.
     *
     * @return array{success: bool, output: string, execution_time_ms: int, applied_count: int}
     *
     * @throws TenantMigrationException
     */
    public function migrate(TenantDatabase $database, ?string $path = null): array
    {
        $this->connectionManager->configureForProvisioning($database);
        $migrationPath = $path ?? self::DEFAULT_MIGRATION_PATH;

        $startTime = microtime(true);

        try {
            $exitCode = Artisan::call('migrate', [
                '--database' => $this->connectionManager->getConnectionName(),
                '--path' => $migrationPath,
                '--force' => true,
            ]);

            $output = Artisan::output();

            if ($exitCode !== 0) {
                throw new TenantMigrationException(
                    sprintf('Migration command failed with exit code %d: %s', $exitCode, $output)
                );
            }

            $executionTimeMs = (int) round((microtime(true) - $startTime) * 1000);

            // Record into landlord tenant_migration_versions table
            $appliedCount = $this->syncMigrationVersions($database, $executionTimeMs);

            $database->update([
                'last_migrated_at' => now(),
            ]);

            return [
                'success' => true,
                'output' => $output,
                'execution_time_ms' => $executionTimeMs,
                'applied_count' => $appliedCount,
            ];
        } catch (Throwable $e) {
            if ($e instanceof TenantMigrationException) {
                throw $e;
            }

            throw new TenantMigrationException(
                sprintf('Tenant migration failed for database [%s]: %s', $database->database, $e->getMessage()),
                $e
            );
        }
    }

    /**
     * Rollback tenant migrations.
     *
     * @return array{success: bool, output: string}
     *
     * @throws TenantMigrationException
     */
    public function rollback(TenantDatabase $database, int $step = 1, ?string $path = null): array
    {
        $this->connectionManager->configureForProvisioning($database);
        $migrationPath = $path ?? self::DEFAULT_MIGRATION_PATH;

        try {
            $exitCode = Artisan::call('migrate:rollback', [
                '--database' => $this->connectionManager->getConnectionName(),
                '--path' => $migrationPath,
                '--step' => $step,
                '--force' => true,
            ]);

            $output = Artisan::output();

            if ($exitCode !== 0) {
                throw new TenantMigrationException(
                    sprintf('Rollback command failed with exit code %d: %s', $exitCode, $output)
                );
            }

            return [
                'success' => true,
                'output' => $output,
            ];
        } catch (Throwable $e) {
            if ($e instanceof TenantMigrationException) {
                throw $e;
            }

            throw new TenantMigrationException(
                sprintf('Tenant rollback failed for database [%s]: %s', $database->database, $e->getMessage()),
                $e
            );
        }
    }

    /**
     * Get migration status list for a specific tenant database.
     *
     * @return array<int, array{migration: string, ran: bool, batch: ?int, status: string}>
     */
    public function status(TenantDatabase $database, ?string $path = null): array
    {
        $this->connectionManager->configureForProvisioning($database);
        $migrationPath = base_path($path ?? self::DEFAULT_MIGRATION_PATH);

        if (! File::isDirectory($migrationPath)) {
            return [];
        }

        $files = File::glob($migrationPath.'/*.php');
        $migrated = [];

        $connectionName = $this->connectionManager->getConnectionName();
        if (Schema::connection($connectionName)->hasTable('migrations')) {
            $migrated = DB::connection($connectionName)
                ->table('migrations')
                ->pluck('batch', 'migration')
                ->toArray();
        }

        $status = [];
        foreach ($files as $file) {
            $name = basename($file, '.php');
            $isMigrated = array_key_exists($name, $migrated);

            $status[] = [
                'migration' => $name,
                'ran' => $isMigrated,
                'batch' => $isMigrated ? (int) $migrated[$name] : null,
                'status' => $isMigrated ? 'migrated' : 'pending',
            ];
        }

        return $status;
    }

    /**
     * Synchronize executed migrations from tenant `migrations` table to landlord `tenant_migration_versions`.
     */
    public function syncMigrationVersions(TenantDatabase $database, ?int $executionTimeMs = null): int
    {
        $connectionName = $this->connectionManager->getConnectionName();

        if (! Schema::connection($connectionName)->hasTable('migrations')) {
            return 0;
        }

        $tenantMigrations = DB::connection($connectionName)
            ->table('migrations')
            ->get();

        $count = 0;
        foreach ($tenantMigrations as $m) {
            TenantMigrationVersion::query()->updateOrCreate(
                [
                    'tenant_database_id' => $database->getKey(),
                    'migration' => $m->migration,
                ],
                [
                    'batch' => $m->batch,
                    'applied_at' => now(),
                    'execution_time_ms' => $executionTimeMs,
                ]
            );
            $count++;
        }

        return $count;
    }
}
