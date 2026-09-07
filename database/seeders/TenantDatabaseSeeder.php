<?php

namespace Database\Seeders;

use Database\Seeders\Tenant\TenantDatabaseSeeder as RootTenantSeeder;
use Illuminate\Database\Seeder;

final class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RootTenantSeeder::class);
    }
}
