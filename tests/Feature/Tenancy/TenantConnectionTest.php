<?php

namespace Tests\Feature\Tenancy;

use App\Exceptions\Tenancy\TenantConnectionException;
use App\Tenancy\TenantConnectionManager;
use Tests\TestCase;

class TenantConnectionTest extends TestCase
{
    public function test_tenant_database_can_be_configured_dynamically(): void
    {
        $connections = app(TenantConnectionManager::class);

        $connections->setDatabase('siakad_tenant_a');
        $this->assertSame('siakad_tenant_a', $connections->currentDatabase());
        $this->assertSame('siakad_tenant_a', config('database.connections.tenant.database'));

        $connections->setDatabase('siakad_tenant_b');
        $this->assertSame('siakad_tenant_b', $connections->currentDatabase());
        $this->assertNotSame('siakad_tenant_a', $connections->currentDatabase());

        $connections->disconnect();
        $this->assertNull($connections->currentDatabase());
    }

    public function test_empty_database_name_is_rejected(): void
    {
        $this->expectException(TenantConnectionException::class);

        app(TenantConnectionManager::class)->setDatabase('');
    }
}
