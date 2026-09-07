<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use App\Services\Tenancy\TenantMigrationService;
use Illuminate\Console\Command;

class TenantMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate 
                            {tenant? : Tenant code, slug, or ID}
                            {--fresh : Drop all tables and re-run all migrations}
                            {--seed : Run seeders after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for tenant database(s)';

    /**
     * Execute the console command.
     */
    public function handle(TenantMigrationService $migrationService): int
    {
        $tenantIdentifier = $this->argument('tenant');
        $fresh = (bool) $this->option('fresh');
        $seed = (bool) $this->option('seed');

        $query = Tenant::query()->where('status', '!=', 'archived');

        if ($tenantIdentifier) {
            $query->where(function ($q) use ($tenantIdentifier) {
                $q->where('code', $tenantIdentifier)
                    ->orWhere('slug', $tenantIdentifier)
                    ->orWhere('id', $tenantIdentifier);
            });
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->error('No tenant(s) found to migrate.');

            return self::FAILURE;
        }

        $this->info(sprintf('Migrating %d tenant(s)...', $tenants->count()));

        foreach ($tenants as $tenant) {
            $this->line(sprintf('==> Migrating Tenant: %s (%s)', $tenant->name, $tenant->code));

            $result = $migrationService->migrate($tenant, fresh: $fresh);

            if (! empty($result['output'])) {
                $this->line($result['output']);
            }

            $this->info(sprintf('Tenant %s migrated successfully in %d ms (Schema: %s).',
                $tenant->code,
                $result['execution_time_ms'],
                $result['schema_version']
            ));

            if ($seed) {
                $this->call('tenant:seed', ['tenant' => $tenant->code]);
            }
        }

        return self::SUCCESS;
    }
}
