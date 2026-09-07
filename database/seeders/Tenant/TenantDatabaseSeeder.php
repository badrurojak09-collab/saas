<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantRolePermissionSeeder::class,
            TenantAdminSeeder::class,
            AcademicReferenceSeeder::class,
            OrganizationSeeder::class,
            AcademicPeriodSeeder::class,
            DocumentTypeSeeder::class,
            FeeTypeSeeder::class,
        ]);
    }
}
