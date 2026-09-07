<?php

namespace App\Actions\Landlord;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TenantDatabase;
use Illuminate\Support\Str;

class CreateTenantDatabaseAction
{
    /**
     * Create tenant database metadata record.
     */
    public function execute(Tenant $tenant, array $data = []): TenantDatabase
    {
        $normalizedCode = Str::lower(preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $tenant->code));
        $databaseName = $data['database'] ?? ('siakad_t_'.$normalizedCode);

        /** @var TenantDatabase $database */
        $database = $tenant->database()->create([
            'name' => 'primary',
            'driver' => $data['driver'] ?? 'mysql',
            'host' => $data['db_host'] ?? config('database.connections.tenant.host', '127.0.0.1'),
            'port' => (int) ($data['db_port'] ?? config('database.connections.tenant.port', 3306)),
            'database' => $databaseName,
            'username' => $data['db_username'] ?? config('database.connections.tenant.username', 'root'),
            'password' => $data['db_password'] ?? config('database.connections.tenant.password', ''),
            'status' => TenantDatabaseStatus::PROVISIONING,
            'is_primary' => true,
            'metadata' => $data['metadata'] ?? [],
        ]);

        return $database;
    }
}
