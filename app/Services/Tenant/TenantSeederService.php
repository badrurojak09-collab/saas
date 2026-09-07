<?php

namespace App\Services\Tenant;

use App\Models\Landlord\TenantDatabase;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Exceptions\Provisioning\TenantSeedingException;
use Database\Seeders\Tenant\TenantDatabaseSeeder;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class TenantSeederService
{
    public function __construct(
        protected TenantConnectionManager $connectionManager,
    ) {}

    /**
     * Run modular idempotent seeders on tenant database.
     *
     * @param  class-string  $seederClass
     * @return array{success: bool, output: string}
     *
     * @throws TenantSeedingException
     */
    public function seed(TenantDatabase $database, string $seederClass = TenantDatabaseSeeder::class): array
    {
        $this->connectionManager->configureForProvisioning($database);
        $previousPermissionContext = TenantPermissionContext::enter();

        try {
            $exitCode = Artisan::call('db:seed', [
                '--database' => $this->connectionManager->getConnectionName(),
                '--class' => $seederClass,
                '--force' => true,
            ]);

            $output = Artisan::output();

            if ($exitCode !== 0) {
                throw new TenantSeedingException(
                    sprintf('Seeding command failed with exit code %d: %s', $exitCode, $output)
                );
            }

            return [
                'success' => true,
                'output' => $output,
            ];
        } catch (Throwable $e) {
            if ($e instanceof TenantSeedingException) {
                throw $e;
            }

            throw new TenantSeedingException(
                sprintf('Tenant seeding failed for database [%s]: %s', $database->database, $e->getMessage()),
                $e
            );
        } finally {
            TenantPermissionContext::leave($previousPermissionContext);
        }
    }
}
