<?php

namespace App\Console\Commands;

use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Services\Tenant\TenantHealthCheckService;
use App\Services\Tenant\TenantMigrationService;
use Illuminate\Console\Command;

class TenantProvisionStatusCommand extends Command
{
    protected $signature = 'tenant:provision-status {tenant : Tenant code, slug, or ID}';

    protected $description = 'Display comprehensive provisioning status, database metadata, and health check for a tenant';

    public function handle(
        TenantHealthCheckService $healthCheckService,
        TenantMigrationService $migrationService,
    ): int {
        $tenantIdentifier = (string) $this->argument('tenant');

        /** @var Tenant|null $tenant */
        $tenant = Tenant::query()
            ->where('code', $tenantIdentifier)
            ->orWhere('slug', $tenantIdentifier)
            ->orWhere('id', $tenantIdentifier)
            ->first();

        if (! $tenant) {
            $this->error(sprintf("Tenant '%s' not found.", $tenantIdentifier));

            return self::FAILURE;
        }

        $database = $tenant->database;

        $this->info('=== Tenant Overview ===');
        $this->table(
            ['Property', 'Value'],
            [
                ['ID', (string) $tenant->getKey()],
                ['Code', $tenant->code],
                ['Name', $tenant->name],
                ['Slug', $tenant->slug],
                ['Status', $tenant->status?->value ?? 'N/A'],
                ['Primary Domain', $tenant->primaryDomain?->domain ?? '-'],
            ]
        );

        $this->newLine();
        $this->info('=== Tenant Database Metadata ===');

        if (! $database) {
            $this->warn('No database metadata record found for this tenant.');
        } else {
            $this->table(
                ['Property', 'Value'],
                [
                    ['Database Name', $database->database],
                    ['Driver', $database->driver ?: 'mysql'],
                    ['Host', $database->host ?: '127.0.0.1'],
                    ['Port', (string) ($database->port ?: 3306)],
                    ['Username', $database->username ?: 'root'],
                    ['Password', '[PROTECTED / ENCRYPTED AT REST]'],
                    ['Status', $database->status?->value ?? 'N/A'],
                    ['Last Migrated At', $database->last_migrated_at ? $database->last_migrated_at->toDateTimeString() : 'Never'],
                ]
            );

            $this->newLine();
            $this->info('=== Health Check Probe ===');
            $health = $healthCheckService->check($database);
            $this->table(
                ['Check', 'Result'],
                [
                    ['Connectivity (SELECT 1)', $health['checks']['connectivity'] ? '<info>PASS</info>' : '<error>FAIL</error>'],
                    ['Critical Tables', $health['checks']['critical_tables'] ? '<info>PASS</info>' : '<error>FAIL</error>'],
                    ['Overall Status', $health['healthy'] ? '<info>HEALTHY</info>' : '<error>UNHEALTHY</error>'],
                    ['Latency', sprintf('%.2f ms', $health['latency_ms'])],
                    ['Error', $health['error'] ?? 'None'],
                ]
            );
        }

        $this->newLine();
        $this->info('=== Provisioning Jobs History ===');
        $jobs = ProvisioningJob::query()
            ->where('tenant_id', $tenant->getKey())
            ->latest('created_at')
            ->take(5)
            ->get();

        if ($jobs->isEmpty()) {
            $this->line('No provisioning jobs found.');
        } else {
            $jobRows = [];
            foreach ($jobs as $job) {
                $jobRows[] = [
                    (string) $job->getKey(),
                    $job->job_type,
                    $job->status?->value ?? 'N/A',
                    (string) $job->attempts,
                    $job->started_at ? $job->started_at->toDateTimeString() : '-',
                    $job->completed_at ? $job->completed_at->toDateTimeString() : '-',
                    $job->error_message ? substr($job->error_message, 0, 50).'...' : '-',
                ];
            }

            $this->table(
                ['Job ID', 'Type', 'Status', 'Attempts', 'Started At', 'Completed At', 'Error'],
                $jobRows
            );
        }

        return self::SUCCESS;
    }
}
