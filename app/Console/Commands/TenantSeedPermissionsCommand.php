<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use App\Services\Tenant\TenantMigrationService;
use App\Services\Tenant\TenantSeederService;
use App\Tenancy\Connection\TenantConnectionManager;
use Database\Seeders\Tenant\TenantPermissionSeeder;
use Illuminate\Console\Command;
use Throwable;

class TenantSeedPermissionsCommand extends Command
{
    protected $signature = 'tenant:seed-permissions {tenant? : Tenant code, slug, or ID} {--all : Seed permissions for all tenants} {--force : Force re-seed even if permissions exist} {--skip-migration : Skip running migrations (not recommended)}';

    protected $description = 'Seed or re-seed permissions for a tenant or all tenants (with automatic migrations)';

    public function handle(
        TenantSeederService $seederService,
        TenantMigrationService $migrationService,
        TenantConnectionManager $connectionManager,
    ): int {
        $tenantIdentifier = $this->argument('tenant');
        $seedAll = $this->option('all');
        $force = $this->option('force');
        // Default: run migration unless explicitly skipped
        $runMigration = ! $this->option('skip-migration');

        if (! $seedAll && ! $tenantIdentifier) {
            $this->error('Please provide a tenant identifier or use --all option.');

            return self::FAILURE;
        }

        if ($seedAll) {
            return $this->seedAllTenants($seederService, $migrationService, $connectionManager, $force, $runMigration);
        }

        return $this->seedSingleTenant($tenantIdentifier, $seederService, $migrationService, $connectionManager, $force, $runMigration);
    }

    protected function seedSingleTenant(
        string $tenantIdentifier,
        TenantSeederService $seederService,
        TenantMigrationService $migrationService,
        TenantConnectionManager $connectionManager,
        bool $force,
        bool $runMigration,
    ): int {
        /** @var Tenant|null $tenant */
        $tenant = Tenant::query()
            ->where('code', $tenantIdentifier)
            ->orWhere('slug', $tenantIdentifier)
            ->orWhere('id', $tenantIdentifier)
            ->first();

        if (! $tenant) {
            $this->error("Tenant '{$tenantIdentifier}' not found.");

            return self::FAILURE;
        }

        /** @var TenantDatabase|null $database */
        $database = $tenant->database; // Tenant hasOne database (primary)

        if (! $database) {
            $this->error("Tenant '{$tenant->code}' does not have a primary database.");

            return self::FAILURE;
        }

        $this->info("Processing Tenant [{$tenant->code}] ({$tenant->name})...");

        try {
            // Run migrations first if requested
            if ($runMigration) {
                $this->line("  Running migrations...");
                $migrationResult = $migrationService->migrate($database);

                if (! $migrationResult['success']) {
                    $this->error("  Migration failed: {$migrationResult['output']}");

                    return self::FAILURE;
                }

                $this->info("  Migrations completed ({$migrationResult['applied_count']} migrations applied).");
            }

            // Seed permissions
            $this->line("  Seeding permissions...");
            $result = $seederService->seed($database, TenantPermissionSeeder::class);

            if ($result['success']) {
                $this->info("  Permissions seeded successfully!");

                if ($force) {
                    $this->info("  Cache flushed due to --force option.");
                }

                return self::SUCCESS;
            }

            $this->error("  Failed to seed permissions: {$result['output']}");

            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error("  Error: {$e->getMessage()}");

            if ($this->option('verbose')) {
                $this->error("  " . $e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }

    protected function seedAllTenants(
        TenantSeederService $seederService,
        TenantMigrationService $migrationService,
        TenantConnectionManager $connectionManager,
        bool $force,
        bool $runMigration,
    ): int {
        $tenants = Tenant::query()
            ->where('status', 'active')
            ->with(['database' => fn ($query) => $query->where('is_primary', true)])
            ->get();

        if ($tenants->isEmpty()) {
            $this->info('No active tenants found.');

            return self::SUCCESS;
        }

        $this->info("Processing {$tenants->count()} active tenants...");

        $successCount = 0;
        $failureCount = 0;

        foreach ($tenants as $tenant) {
            /** @var TenantDatabase|null $database */
            $database = $tenant->database;

            if (! $database) {
                $this->warn("  Tenant [{$tenant->code}] skipped - no primary database.");
                $failureCount++;
                continue;
            }

            try {
                $this->line("  Tenant [{$tenant->code}]...");

                // Run migrations first if requested
                if ($runMigration) {
                    $migrationResult = $migrationService->migrate($database);

                    if (! $migrationResult['success']) {
                        $this->error("    ✗ Migration failed: {$migrationResult['output']}");
                        $failureCount++;
                        continue;
                    }

                    $this->info("    ✓ Migrations applied ({$migrationResult['applied_count']})");
                }

                // Seed permissions
                $result = $seederService->seed($database, TenantPermissionSeeder::class);

                if ($result['success']) {
                    $this->info("    ✓ Permissions seeded");
                    $successCount++;
                } else {
                    $this->error("    ✗ Seed failed: {$result['output']}");
                    $failureCount++;
                }
            } catch (Throwable $e) {
                $this->error("    ✗ Error: {$e->getMessage()}");
                $failureCount++;

                if ($this->option('verbose')) {
                    $this->error("      " . $e->getTraceAsString());
                }
            }
        }

        $this->info("");
        $this->info("Summary:");
        $this->info("  Successful: {$successCount}");
        $this->info("  Failed: {$failureCount}");

        return $failureCount === 0 ? self::SUCCESS : self::FAILURE;
    }
}
