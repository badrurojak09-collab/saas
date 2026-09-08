<?php

namespace App\Tenancy\Connection;

use App\Enums\Landlord\TenantDatabaseStatus;
use App\Models\Landlord\TenantDatabase;
use App\Tenancy\Contracts\TenantConnectionManager as TenantConnectionManagerContract;
use App\Tenancy\Exceptions\TenantConnectionException;
use App\Tenancy\Exceptions\TenantDatabaseUnavailableException;
use Illuminate\Support\Facades\DB;
use Throwable;

class TenantConnectionManager implements TenantConnectionManagerContract
{
    private string $connectionName;

    public function __construct()
    {
        $this->connectionName = config('tenancy.tenant_connection', 'tenant');
    }

    public function configure(TenantDatabase $database): void
    {
        $status = $database->status instanceof TenantDatabaseStatus
            ? $database->status
            : TenantDatabaseStatus::tryFrom((string) $database->status);

        if ($status !== TenantDatabaseStatus::READY) {
            $statusName = $status ? $status->value : 'unconfigured';
            throw new TenantDatabaseUnavailableException(
                "Tenant database is currently [{$statusName}]."
            );
        }

        $this->applyConfiguration($database);
    }

    public function configureForProvisioning(TenantDatabase $database): void
    {
        $this->applyConfiguration($database);
    }

    protected function applyConfiguration(TenantDatabase $database): void
    {
        if (empty($database->database)) {
            throw new TenantConnectionException('Tenant database name is empty.');
        }

        $driver = strtolower($database->driver ?: (str_ends_with($database->database, '.sqlite') ? 'sqlite' : 'mysql'));

        if ($driver === 'sqlite' && $database->database !== ':memory:' && ! str_ends_with(strtolower($database->database), '.sqlite')) {
            throw new TenantConnectionException(
                "Tenant database [{$database->database}] is configured as SQLite, but it is not a SQLite file path. Set driver to mysql for a MySQL database name."
            );
        }

        if ($driver === 'sqlite') {
            config([
                "database.connections.{$this->connectionName}" => [
                    'driver' => 'sqlite',
                    'database' => $database->database,
                    'prefix' => '',
                    'foreign_key_constraints' => true,
                ],
            ]);
        } else {
            config([
                "database.connections.{$this->connectionName}" => [
                    'driver' => 'mysql',
                    'host' => $database->host ?: '127.0.0.1',
                    'port' => (int) ($database->port ?: 3306),
                    'database' => $database->database,
                    'username' => $database->username ?: 'root',
                    'password' => $database->password, // decrypted in-memory by Eloquent cast
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                ],
            ]);
        }

        $this->purge();
    }

    public function connect(): void
    {
        try {
            DB::reconnect($this->connectionName);
            DB::connection($this->connectionName)->getPdo();
        } catch (TenantDatabaseUnavailableException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new TenantConnectionException(
                'Failed to connect to tenant database.',
                previous: $e,
            );
        }
    }

    public function disconnect(): void
    {
        $this->purge();

        config([
            "database.connections.{$this->connectionName}.database" => null,
        ]);
    }

    public function purge(): void
    {
        DB::purge($this->connectionName);
    }

    public function reconnect(): void
    {
        $this->purge();
        $this->connect();
    }

    public function isConnected(): bool
    {
        try {
            return DB::connection($this->connectionName)->getPdo() !== null;
        } catch (Throwable) {
            return false;
        }
    }

    public function getCurrentDatabase(): ?string
    {
        return config("database.connections.{$this->connectionName}.database");
    }

    public function getConnectionName(): string
    {
        return $this->connectionName;
    }
}
