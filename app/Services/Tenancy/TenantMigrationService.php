<?php

namespace App\Services\Tenancy;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Models\Landlord\TenantMigrationVersion;
use App\Tenancy\TenantManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TenantMigrationService
{
    public const MIGRATION_PATH = 'database/migrations/tenant';

    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    /**
     * Run migrations for a specific tenant.
     */
    public function migrate(Tenant $tenant, bool $fresh = false, ?string $path = null): array
    {
        $this->tenantManager->initialize($tenant, connect: true);
        $migrationPath = $path ?? self::MIGRATION_PATH;

        $startTime = microtime(true);

        if ($fresh) {
            Artisan::call('migrate:fresh', [
                '--database' => 'tenant',
                '--path' => $migrationPath,
                '--force' => true,
            ]);
        } else {
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => $migrationPath,
                '--force' => true,
            ]);
        }

        $output = Artisan::output();
        $executionTimeMs = (int) round((microtime(true) - $startTime) * 1000);

        // Record in landlord database metadata if tenantDatabase exists
        $tenantDb = $tenant->database;
        if ($tenantDb) {
            $tenantDb->update([
                'last_migrated_at' => now(),
                'schema_version' => $this->getSchemaVersion($tenant),
            ]);

            // Save migration versions to tenant_migration_versions table
            $this->syncMigrationVersions($tenantDb);
        }

        return [
            'success' => true,
            'output' => $output,
            'execution_time_ms' => $executionTimeMs,
            'schema_version' => $this->getSchemaVersion($tenant),
        ];
    }

    /**
     * Rollback migrations for a specific tenant.
     */
    public function rollback(Tenant $tenant, int $steps = 1, ?string $path = null): array
    {
        $this->tenantManager->initialize($tenant, connect: true);
        $migrationPath = $path ?? self::MIGRATION_PATH;

        Artisan::call('migrate:rollback', [
            '--database' => 'tenant',
            '--path' => $migrationPath,
            '--step' => $steps,
            '--force' => true,
        ]);

        $output = Artisan::output();

        $tenantDb = $tenant->database;
        if ($tenantDb) {
            $tenantDb->update([
                'schema_version' => $this->getSchemaVersion($tenant),
            ]);
            $this->syncMigrationVersions($tenantDb);
        }

        return [
            'success' => true,
            'output' => $output,
        ];
    }

    /**
     * Get migration status for a specific tenant.
     */
    public function status(Tenant $tenant, ?string $path = null): array
    {
        $this->tenantManager->initialize($tenant, connect: true);
        $migrationPath = base_path($path ?? self::MIGRATION_PATH);

        if (! File::isDirectory($migrationPath)) {
            return [];
        }

        $files = File::glob($migrationPath.'/*.php');
        $migrated = [];

        if (Schema::connection('tenant')->hasTable('migrations')) {
            $migrated = DB::connection('tenant')
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
                'batch' => $isMigrated ? $migrated[$name] : null,
                'status' => $isMigrated ? 'migrated' : 'pending',
            ];
        }

        return $status;
    }

    /**
     * Check tenant database health.
     */
    public function health(Tenant $tenant): array
    {
        $startTime = microtime(true);

        try {
            $this->tenantManager->initialize($tenant, connect: true);
            DB::connection('tenant')->getPdo();
            $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

            $hasMigrationsTable = Schema::connection('tenant')->hasTable('migrations');
            $statusList = $this->status($tenant);
            $pendingCount = count(array_filter($statusList, fn ($item) => ! $item['ran']));
            $tables = Schema::connection('tenant')->getTableListing();

            $isHealthy = $hasMigrationsTable && $pendingCount === 0;

            return [
                'tenant' => $tenant->code,
                'database' => $tenant->getDatabaseName(),
                'connection' => 'OK',
                'migration' => $pendingCount === 0 ? 'OK' : "PENDING ({$pendingCount})",
                'schema' => $this->getSchemaVersion($tenant),
                'latency' => "{$latencyMs} ms",
                'table_count' => count($tables),
                'status' => $isHealthy ? 'HEALTHY' : 'NEEDS_MIGRATION',
            ];
        } catch (Throwable $e) {
            return [
                'tenant' => $tenant->code,
                'database' => $tenant->getDatabaseName(),
                'connection' => 'FAILED: '.$e->getMessage(),
                'migration' => 'UNKNOWN',
                'schema' => 'UNKNOWN',
                'latency' => '0 ms',
                'table_count' => 0,
                'status' => 'UNHEALTHY',
            ];
        }
    }

    /**
     * Resolve schema version.
     */
    protected function getSchemaVersion(Tenant $tenant): string
    {
        try {
            if (Schema::connection('tenant')->hasTable('system_metadata')) {
                $version = DB::connection('tenant')
                    ->table('system_metadata')
                    ->where('key', 'schema_version')
                    ->value('value');

                if ($version) {
                    return $version;
                }
            }
        } catch (Throwable) {
            // Ignore
        }

        return '1.0.0';
    }

    /**
     * Sync migration records to Landlord tenant_migration_versions table.
     */
    protected function syncMigrationVersions(TenantDatabase $tenantDb): void
    {
        try {
            if (! Schema::connection('tenant')->hasTable('migrations')) {
                return;
            }

            $tenantMigrations = DB::connection('tenant')
                ->table('migrations')
                ->get();

            foreach ($tenantMigrations as $m) {
                TenantMigrationVersion::query()->updateOrCreate(
                    [
                        'tenant_database_id' => $tenantDb->id,
                        'migration' => $m->migration,
                    ],
                    [
                        'batch' => $m->batch,
                        'applied_at' => now(),
                    ]
                );
            }
        } catch (Throwable) {
            // Landlord table sync failure should not break tenant migration flow
        }
    }
}
