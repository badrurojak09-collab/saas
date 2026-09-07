<?php

namespace App\Tenancy\Contracts;

use App\Models\Landlord\TenantDatabase;
use App\Tenancy\Exceptions\TenantConnectionException;
use App\Tenancy\Exceptions\TenantDatabaseUnavailableException;

interface TenantConnectionManager
{
    /**
     * Configure the dynamic 'tenant' connection using database metadata.
     *
     * @throws TenantDatabaseUnavailableException
     */
    public function configure(TenantDatabase $database): void;

    /**
     * Configure the dynamic 'tenant' connection during provisioning, bypassing the READY status requirement.
     */
    public function configureForProvisioning(TenantDatabase $database): void;

    /**
     * Establish the connection to the configured tenant database.
     *
     * @throws TenantConnectionException
     */
    public function connect(): void;

    /**
     * Disconnect and clear tenant connection configuration.
     */
    public function disconnect(): void;

    /**
     * Purge the active tenant connection from Laravel DB connection pool.
     */
    public function purge(): void;

    /**
     * Reconnect to the tenant database.
     *
     * @throws TenantConnectionException
     */
    public function reconnect(): void;

    /**
     * Check if the tenant connection is currently connected.
     */
    public function isConnected(): bool;

    /**
     * Retrieve the currently configured tenant database name.
     */
    public function getCurrentDatabase(): ?string;
}
