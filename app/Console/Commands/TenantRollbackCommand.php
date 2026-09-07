<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use App\Services\Tenancy\TenantMigrationService;
use Illuminate\Console\Command;

class TenantRollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:rollback 
                            {tenant : Tenant code, slug, or ID}
                            {--step=1 : The number of migrations to rollback}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback migrations for a specific tenant database';

    /**
     * Execute the console command.
     */
    public function handle(TenantMigrationService $migrationService): int
    {
        $tenantIdentifier = $this->argument('tenant');
        $step = (int) $this->option('step');

        $tenant = Tenant::query()
            ->where('code', $tenantIdentifier)
            ->orWhere('slug', $tenantIdentifier)
            ->orWhere('id', $tenantIdentifier)
            ->first();

        if (! $tenant) {
            $this->error(sprintf("Tenant '%s' not found.", $tenantIdentifier));

            return self::FAILURE;
        }

        $this->info(sprintf('Rolling back %d step(s) for tenant: %s (%s)...', $step, $tenant->name, $tenant->code));

        $result = $migrationService->rollback($tenant, steps: $step);

        if (! empty($result['output'])) {
            $this->line($result['output']);
        }

        $this->info(sprintf('Tenant %s rollback completed.', $tenant->code));

        return self::SUCCESS;
    }
}
