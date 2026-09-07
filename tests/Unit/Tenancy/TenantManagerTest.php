<?php

namespace Tests\Unit\Tenancy;

use App\Tenancy\TenantIdentity;
use App\Tenancy\TenantManager;
use Tests\TestCase;

class TenantManagerTest extends TestCase
{
    public function test_initialize_sets_context_and_database_config(): void
    {
        $manager = app(TenantManager::class);

        $a = new TenantIdentity('tenant-a', 'siakad_tenant_a');
        $b = new TenantIdentity('tenant-b', 'siakad_tenant_b');

        $manager->initialize($a);

        $this->assertTrue($manager->has());
        $this->assertSame('tenant-a', $manager->current()->getTenantKey());
        $this->assertSame('siakad_tenant_a', config('database.connections.tenant.database'));

        $manager->initialize($b);

        $this->assertSame('tenant-b', $manager->current()->getTenantKey());
        $this->assertSame('siakad_tenant_b', config('database.connections.tenant.database'));
        $this->assertNotSame('siakad_tenant_a', config('database.connections.tenant.database'));

        $manager->end();

        $this->assertFalse($manager->has());
        $this->assertNull(config('database.connections.tenant.database'));
    }
}
