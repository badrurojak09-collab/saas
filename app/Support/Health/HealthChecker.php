<?php

namespace App\Support\Health;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class HealthChecker
{
    /**
     * @return array{
     *     status: string,
     *     application: string,
     *     php: string,
     *     laravel: string,
     *     database: string,
     *     tenant_database: string,
     *     redis: string,
     *     queue: string,
     *     storage: string
     * }
     */
    public function check(): array
    {
        $checks = [
            'application' => 'ok',
            'php' => version_compare(PHP_VERSION, '8.3.0', '>=') ? 'ok' : 'fail',
            'laravel' => app()->version() !== '' ? 'ok' : 'fail',
            'database' => $this->landlord(),
            'tenant_database' => $this->tenantDatabase(),
            'redis' => $this->redis(),
            'queue' => $this->queue(),
            'storage' => $this->storage(),
        ];

        return [
            'status' => $this->overall($checks),
            ...$checks,
        ];
    }

    private function landlord(): string
    {
        try {
            DB::connection('landlord')->getPdo();

            return 'ok';
        } catch (Throwable) {
            return 'fail';
        }
    }

    private function tenantDatabase(): string
    {
        $connection = config('database.connections.tenant');

        if (! is_array($connection) || ($connection['driver'] ?? null) !== 'mysql') {
            return 'fail';
        }

        return config('database.connections.tenant.database') === null
            ? 'configured'
            : 'active';
    }

    private function redis(): string
    {
        $usesRedis = in_array('redis', [
            config('cache.default'),
            config('queue.default'),
            config('session.driver'),
        ], true);

        if (! $usesRedis) {
            return 'skipped';
        }

        try {
            Redis::connection()->ping();

            return 'ok';
        } catch (Throwable) {
            return 'fail';
        }
    }

    private function queue(): string
    {
        $connection = config('queue.default');

        return is_string($connection) && $connection !== '' ? 'ok' : 'fail';
    }

    private function storage(): string
    {
        try {
            $disk = Storage::disk(config('filesystems.default', 'local'));
            $disk->put('.healthcheck', 'ok');
            $disk->delete('.healthcheck');

            return 'ok';
        } catch (Throwable) {
            return 'fail';
        }
    }

    /**
     * @param  array<string, string>  $checks
     */
    private function overall(array $checks): string
    {
        foreach (['application', 'php', 'laravel', 'database'] as $key) {
            if (($checks[$key] ?? null) !== 'ok') {
                return 'error';
            }
        }

        return 'ok';
    }
}
