<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use App\Services\Tenancy\TenantMigrationService;
use Illuminate\Console\Command;

class TenantStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:status {tenant : Tenant code, slug, or ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show migration status for a specific tenant database';

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

        $this->line(sprintf('Tenant: %s', $tenant->code));
        $this->line(sprintf('Database: %s', $tenant->getDatabaseName() ?? 'N/A'));
        $this->newLine();
        $this->line('Migration Status');
        $this->line('----------------');

        $statusList = $migrationService->status($tenant);

        if (empty($statusList)) {
            $this->warn('No migrations found.');

            return self::SUCCESS;
        }

        $tableRows = [];
        foreach ($statusList as $item) {
            $tableRows[] = [
                $item['migration'],
                $item['ran'] ? '<info>migrated</info>' : '<comment>pending</comment>',
                $item['batch'] ?? '-',
            ];
        }

        $this->table(['Migration', 'Status', 'Batch'], $tableRows);

        return self::SUCCESS;
    }
}
