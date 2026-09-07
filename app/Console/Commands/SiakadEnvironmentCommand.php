<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SiakadEnvironmentCommand extends Command
{
    protected $signature = 'siakad:environment';

    protected $description = 'Show SIAKAD environment (no secrets)';

    public function handle(): int
    {
        $this->table(
            ['Key', 'Value'],
            [
                ['APP_NAME', config('app.name')],
                ['APP_ENV', config('app.env')],
                ['APP_DEBUG', config('app.debug') ? 'true' : 'false'],
                ['APP_URL', config('app.url')],
                ['TIMEZONE', config('app.timezone')],
                ['LOCALE', config('app.locale')],
                ['PHP', PHP_VERSION],
                ['LARAVEL', app()->version()],
                ['DEFAULT_DB', config('database.default')],
                ['LANDLORD_DB', config('database.connections.landlord.database')],
                ['TENANT_DB', config('database.connections.tenant.database') ?? 'null (dynamic)'],
                ['CACHE', config('cache.default')],
                ['QUEUE', config('queue.default')],
                ['SESSION', config('session.driver')],
                ['FILESYSTEM', config('filesystems.default')],
            ],
        );

        return self::SUCCESS;
    }
}
