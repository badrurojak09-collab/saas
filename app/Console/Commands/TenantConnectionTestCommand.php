<?php

namespace App\Console\Commands;

use App\Services\Tenant\TenantDatabaseService;
use App\Tenancy\Contracts\TenantResolver;
use Illuminate\Console\Command;
use Throwable;

class TenantConnectionTestCommand extends Command
{
    protected $signature = 'tenant:connection-test {tenant : Tenant code or ID}';

    protected $description = 'Test the database connection for a specific tenant.';

    public function handle(TenantResolver $resolver, TenantDatabaseService $dbService): int
    {
        $tenantIdentifier = $this->argument('tenant');

        try {
            $tenant = $resolver->resolveFromId($tenantIdentifier);
            $database = $dbService->loadMetadata($tenant);

            $this->line("Testing connection for Tenant: {$tenant->code} ({$tenant->name})...");

            $result = $dbService->testConnection($database);

            if ($result['connected']) {
                $this->info("Tenant: {$tenant->code}");
                $this->line("Database: {$database->database}");
                $this->info('Connection: OK');
                $this->line("Latency: {$result['latency_ms']} ms");

                return self::SUCCESS;
            }

            $this->error('Connection failed: '.($result['error'] ?? 'Unknown error'));

            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
