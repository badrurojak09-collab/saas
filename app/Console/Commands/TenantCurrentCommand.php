<?php

namespace App\Console\Commands;

use App\Tenancy\Contracts\TenantConnectionManager;
use App\Tenancy\Contracts\TenantManager;
use Illuminate\Console\Command;

class TenantCurrentCommand extends Command
{
    protected $signature = 'tenant:current';

    protected $description = 'Display the currently initialized tenant context.';

    public function handle(TenantManager $manager, TenantConnectionManager $connectionManager): int
    {
        if (! $manager->isInitialized()) {
            $this->warn('No tenant context initialized.');

            return self::SUCCESS;
        }

        $tenant = $manager->current();

        $this->info('Tenant');
        $this->line('------');
        $this->line('ID: '.$tenant->getKey());
        $this->line('Code: '.$tenant->code);
        $this->line('Name: '.$tenant->name);
        $this->line('Database: '.($connectionManager->getCurrentDatabase() ?: $tenant->getDatabaseName()));
        $this->line('Status: '.($tenant->status->value ?? $tenant->status));
        $this->line('Connection: '.($connectionManager->isConnected() ? 'connected' : 'disconnected'));

        return self::SUCCESS;
    }
}
