<?php

namespace Tests\Unit\Tenant\Architecture;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Boundary architecture tests for Tenant migrations.
 * 
 * Enforces Sprint 03 architectural rules:
 * - No tenant_id column in any tenant migration (tenant DB is self-contained)
 * - No foreign key references to a landlord database table
 * - Every tenant migration file is in the correct directory
 */
final class MigrationBoundaryTest extends TestCase
{
    private string $tenantMigrationPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenantMigrationPath = base_path('database/migrations/tenant');
    }

    public function test_tenant_migration_directory_exists(): void
    {
        $this->assertTrue(
            File::isDirectory($this->tenantMigrationPath),
            'Tenant migration directory must exist at database/migrations/tenant'
        );
    }

    public function test_no_tenant_migration_contains_tenant_id_column(): void
    {
        $files = File::glob($this->tenantMigrationPath . '/*.php');
        $this->assertNotEmpty($files, 'No tenant migration files found.');

        $violations = [];
        foreach ($files as $file) {
            $content = File::get($file);
            // Check for tenant_id column definition patterns
            if (
                preg_match('/\$table->(string|unsignedBigInteger|foreignId)\(\'tenant_id\'\)/', $content) ||
                preg_match('/->references.*tenant.*->on/', $content)
            ) {
                $violations[] = basename($file);
            }
        }

        $this->assertEmpty(
            $violations,
            'Tenant migrations must NOT have tenant_id column or FK to landlord. Violations: ' . implode(', ', $violations)
        );
    }

    public function test_no_tenant_migration_references_landlord_tables(): void
    {
        $landlordTables = ['tenants', 'tenant_databases', 'tenant_subscriptions', 'domains'];
        $files = File::glob($this->tenantMigrationPath . '/*.php');

        $violations = [];
        foreach ($files as $file) {
            $content = File::get($file);
            foreach ($landlordTables as $table) {
                if (str_contains($content, "->on('{$table}')") || str_contains($content, "->references('{$table}')")) {
                    $violations[] = basename($file) . " references landlord table '{$table}'";
                }
            }
        }

        $this->assertEmpty(
            $violations,
            'Tenant migrations must NOT reference Landlord tables. Violations: ' . implode("\n", $violations)
        );
    }

    public function test_all_tenant_migration_files_are_php(): void
    {
        $nonPhpFiles = collect(File::allFiles($this->tenantMigrationPath))
            ->filter(fn ($file) => $file->getExtension() !== 'php')
            ->map(fn ($file) => $file->getFilename())
            ->values()
            ->all();

        $this->assertEmpty($nonPhpFiles, 'All files in tenant migration dir must be .php: ' . implode(', ', $nonPhpFiles));
    }

    public function test_tenant_migrations_have_correct_naming_convention(): void
    {
        $files = File::glob($this->tenantMigrationPath . '/*.php');

        $violations = [];
        foreach ($files as $file) {
            $basename = basename($file);
            // Pattern: YYYY_MM_DD_HHMMSS_name.php or 0001_01_01_HHMMSS_name.php
            if (! preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_[a-z0-9_]+\.php$/', $basename)) {
                $violations[] = $basename;
            }
        }

        $this->assertEmpty(
            $violations,
            'Migrations must follow Laravel naming convention: ' . implode(', ', $violations)
        );
    }

    public function test_tenant_migration_count_matches_expected(): void
    {
        $files = File::glob($this->tenantMigrationPath . '/*.php');
        $count = count($files);

        $this->assertGreaterThanOrEqual(
            38,
            $count,
            "Expected at least 38 tenant migrations, found {$count}"
        );
    }
}
