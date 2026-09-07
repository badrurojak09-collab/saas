<?php

namespace App\Tenancy;

use App\Exceptions\Tenancy\TenantConnectionException;
use Illuminate\Support\Facades\DB;
use Throwable;

final class TenantConnectionManager
{
    public const CONNECTION = 'tenant';

    public function setDatabase(?string $database): void
    {
        if ($database === null || $database === '') {
            throw new TenantConnectionException('Tenant database name is empty.');
        }

        config([
            'database.connections.'.self::CONNECTION.'.database' => $database,
        ]);

        DB::purge(self::CONNECTION);
    }

    public function reconnect(): void
    {
        try {
            DB::reconnect(self::CONNECTION);
            DB::connection(self::CONNECTION)->getPdo();
        } catch (Throwable $e) {
            throw new TenantConnectionException(
                'Failed to connect to tenant database: '.$e->getMessage(),
                previous: $e,
            );
        }
    }

    public function disconnect(): void
    {
        DB::purge(self::CONNECTION);

        config([
            'database.connections.'.self::CONNECTION.'.database' => null,
        ]);
    }

    public function currentDatabase(): ?string
    {
        return config('database.connections.'.self::CONNECTION.'.database');
    }
}
