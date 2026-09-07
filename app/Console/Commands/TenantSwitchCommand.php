<?php

namespace App\Console\Commands;

use App\Tenancy\Contracts\TenantConnectionManager;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use Illuminate\Console\Command;
use Throwable;

class TenantSwitchCommand extends Command
{
    protected $signature = 'tenant:switch {tenant : Tenant code or ID}';

    protected $description = 'Switch active tenant context for the current CLI session.';

    public function handle(
        TenantResolver $resolver,
        TenantManager $manager,
        TenantConnectionManager $connectionManager,
    ): int {
        $tenantIdentifier = $this->argument('tenant');

        try {
            $tenant = $resolver->resolveFromId($tenantIdentifier);

            if ($manager->isInitialized()) {
                $manager->end();
            }

            $manager->initialize($tenant);

            $this->info("Switched to Tenant [{$tenant->code}] ({$tenant->name}).");
            $this->line('Database: '.($connectionManager->getCurrentDatabase() ?: 'unspecified'));

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed to switch tenant: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
