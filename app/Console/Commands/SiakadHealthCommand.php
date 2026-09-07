<?php

namespace App\Console\Commands;

use App\Support\Health\HealthChecker;
use Illuminate\Console\Command;

class SiakadHealthCommand extends Command
{
    protected $signature = 'siakad:health';

    protected $description = 'Run SIAKAD infrastructure health check';

    public function handle(HealthChecker $checker): int
    {
        $result = $checker->check();

        $this->newLine();
        $this->info('SIAKAD Health Check');
        $this->newLine();

        $this->table(
            ['Check', 'Status'],
            [
                ['Application', strtoupper($result['application'])],
                ['PHP', strtoupper($result['php'])],
                ['Laravel', strtoupper($result['laravel'])],
                ['Landlord DB', strtoupper($result['database'])],
                ['Tenant DB', strtoupper($result['tenant_database'])],
                ['Redis', strtoupper($result['redis'])],
                ['Queue', strtoupper($result['queue'])],
                ['Storage', strtoupper($result['storage'])],
            ],
        );

        $this->newLine();
        $this->line('Overall: '.strtoupper($result['status']));

        return $result['status'] === 'ok' ? self::SUCCESS : self::FAILURE;
    }
}
