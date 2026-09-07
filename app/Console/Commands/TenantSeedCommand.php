<?php

namespace App\Console\Commands;

use App\Models\Landlord\Tenant;
use App\Tenancy\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed 
                            {tenant? : Tenant code, slug, or ID}
                            {--class=Database\\Seeders\\Tenant\\TenantDatabaseSeeder : The class name of the root seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed tenant database(s)';

    /**
     * Execute the console command.
     */
    public function handle(TenantManager $tenantManager): int
    {
        $tenantIdentifier = $this->argument('tenant');
        $seederClass = $this->option('class');

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
            $this->error('No tenant(s) found to seed.');

            return self::FAILURE;
        }

        foreach ($tenants as $tenant) {
            $this->line(sprintf('==> Seeding Tenant: %s (%s)', $tenant->name, $tenant->code));

            $tenantManager->initialize($tenant, connect: true);

            Artisan::call('db:seed', [
                '--database' => 'tenant',
                '--class' => $seederClass,
                '--force' => true,
            ]);

            $this->line(Artisan::output());
            $this->info(sprintf('Tenant %s seeded successfully.', $tenant->code));
        }

        return self::SUCCESS;
    }
}
