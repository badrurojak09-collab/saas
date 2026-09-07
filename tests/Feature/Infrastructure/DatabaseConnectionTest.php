<?php

namespace Tests\Feature\Infrastructure;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function test_landlord_and_tenant_connections_are_defined(): void
    {
        $this->assertArrayHasKey('landlord', config('database.connections'));
        $this->assertArrayHasKey('tenant', config('database.connections'));

        $this->assertSame('mysql', config('database.connections.landlord.driver'));
        $this->assertSame('mysql', config('database.connections.tenant.driver'));
        $this->assertNull(config('database.connections.tenant.database'));
    }

    public function test_landlord_can_connect_when_available(): void
    {
        try {
            DB::connection('landlord')->getPdo();
        } catch (\Throwable $e) {
            $this->markTestSkipped('Landlord MySQL is not available: '.$e->getMessage());
        }

        $this->assertTrue(true);
    }
}
