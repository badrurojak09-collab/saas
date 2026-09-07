<?php

namespace Tests\Unit\Tenancy;

use App\Exceptions\Tenancy\TenantNotResolvedException;
use App\Tenancy\TenantContext;
use App\Tenancy\TenantIdentity;
use Tests\TestCase;

class TenantContextTest extends TestCase
{
    public function test_set_current_has_and_clear(): void
    {
        $context = new TenantContext;
        $tenant = new TenantIdentity('tenant-a', 'siakad_tenant_a');

        $this->assertFalse($context->has());

        $context->set($tenant);

        $this->assertTrue($context->has());
        $this->assertSame($tenant, $context->current());
        $this->assertSame('tenant-a', $context->current()->getTenantKey());

        $context->clear();

        $this->assertFalse($context->has());
    }

    public function test_current_throws_when_empty(): void
    {
        $this->expectException(TenantNotResolvedException::class);

        (new TenantContext)->current();
    }
}
