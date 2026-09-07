<?php

namespace Tests\Feature;

use App\Models\Landlord\Domain;
use App\Models\Landlord\Package;
use App\Models\Landlord\PlatformUser;
use App\Models\Landlord\Tenant;
use Database\Seeders\LocalDevelopmentSeeder;
use Tests\TestCase;

final class LocalDevelopmentSeederTest extends TestCase
{
    public function test_local_development_seeder_runs_successfully(): void
    {
        $this->seed(LocalDevelopmentSeeder::class);

        $this->assertDatabaseHas('platform_users', [
            'email' => 'admin@siakad.test',
        ], 'landlord');

        $this->assertDatabaseHas('packages', [
            'code' => 'PRO',
        ], 'landlord');

        $this->assertDatabaseHas('tenants', [
            'code' => 'DEMO',
        ], 'landlord');

        $this->assertDatabaseHas('domains', [
            'domain' => 'localhost',
        ], 'landlord');
    }
}
