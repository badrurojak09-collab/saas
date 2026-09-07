<?php

namespace Tests\Integration\Tenancy;

use App\Tenancy\Services\TenantResolverService;
use Tests\TestCase;

class TenantResolverTest extends TestCase
{
    private TenantResolverService $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new TenantResolverService();
    }

    public function test_domain_normalization(): void
    {
        $this->assertSame('kampus-a.siakad.test', $this->resolver->normalizeDomain('Kampus-A.Siakad.Test'));
        $this->assertSame('kampus-a.siakad.test', $this->resolver->normalizeDomain('kampus-a.siakad.test:8000'));
        $this->assertSame('kampus-a.siakad.test', $this->resolver->normalizeDomain('kampus-a.siakad.test.'));
        $this->assertSame('kampus-a.siakad.test', $this->resolver->normalizeDomain('  KAMPUS-A.SIAKAD.TEST:443. '));
    }

    public function test_unknown_domain_throws_domain_not_found_exception(): void
    {
        $this->expectException(\App\Tenancy\Exceptions\TenantDomainNotFoundException::class);

        $this->resolver->resolveFromHost('non-existent-domain-xyz.example.com');
    }

    public function test_unknown_tenant_id_throws_not_found_exception(): void
    {
        $this->expectException(\App\Tenancy\Exceptions\TenantNotFoundException::class);

        $this->resolver->resolveFromId('01999999-9999-7999-8999-999999999999');
    }
}
