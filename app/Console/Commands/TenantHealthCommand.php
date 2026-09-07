<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use App\Services\Tenancy\TenantMigrationService;
use Illuminate\Console\Command;

class TenantHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:health {tenant : Tenant code, slug, or ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check health and database connectivity of a tenant';

    /**
     * Execute the console command.
     */
    public function handle(TenantMigrationService $migrationService): int
    {
        $tenantIdentifier = $this->argument('tenant');

        $tenant = Tenant::query()
            ->where('code', $tenantIdentifier)
            ->orWhere('slug', $tenantIdentifier)
            ->orWhere('id', $tenantIdentifier)
            ->first();

        if (! $tenant) {
            $this->error(sprintf("Tenant '%s' not found.", $tenantIdentifier));

            return self::FAILURE;
        }

        $health = $migrationService->health($tenant);

        $this->newLine();
        $this->line(sprintf('Tenant: %s', $health['tenant']));
        $this->line(sprintf('Database: %s', $health['database']));
        $this->line(sprintf('Connection: %s', $health['connection']));
        $this->line(sprintf('Migration: %s', $health['migration']));
        $this->line(sprintf('Schema: %s', $health['schema']));
        $this->line(sprintf('Latency: %s', $health['latency']));
        $this->line(sprintf('Tables: %d', $health['table_count']));

        if ($health['status'] === 'HEALTHY') {
            $this->info(sprintf('Status: %s', $health['status']));

            return self::SUCCESS;
        }

        $this->error(sprintf('Status: %s', $health['status']));

        return self::FAILURE;
    }
}
