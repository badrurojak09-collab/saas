<?php

namespace Tests\Integration\Tenancy;

use App\Models\Landlord\Tenant;
use App\Tenancy\Context\TenantContext;
use App\Tenancy\Exceptions\TenantContextMissingException;
use Tests\TestCase;

class TenantContextTest extends TestCase
{
    private TenantContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new TenantContext();
    }

    public function test_context_starts_empty(): void
    {
        $this->assertFalse($this->context->has());
        $this->assertNull($this->context->get());
    }

    public function test_require_throws_exception_when_context_is_empty(): void
    {
        $this->expectException(TenantContextMissingException::class);

        $this->context->require();
    }

    public function test_can_set_and_retrieve_tenant(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000001';
        $tenant->code = 'INST-TEST';

        $this->context->set($tenant);

        $this->assertTrue($this->context->has());
        $this->assertSame($tenant, $this->context->get());
        $this->assertSame($tenant, $this->context->require());
    }

    public function test_can_clear_context(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000001';

        $this->context->set($tenant);
        $this->assertTrue($this->context->has());

        $this->context->clear();
        $this->assertFalse($this->context->has());
        $this->assertNull($this->context->get());
    }
}
