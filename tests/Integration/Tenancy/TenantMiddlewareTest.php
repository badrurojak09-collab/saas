<?php

namespace Tests\Integration\Tenancy;

use App\Models\Landlord\Tenant;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use App\Tenancy\Middleware\InitializeTenant;
use App\Tenancy\Middleware\PreventTenantLeakage;
use App\Tenancy\Middleware\ResolveTenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class TenantMiddlewareTest extends TestCase
{
    public function test_resolve_tenant_middleware_attaches_tenant_to_request(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000001';
        $tenant->code = 'INST-001';

        $resolver = $this->createMock(TenantResolver::class);
        $resolver->expects($this->once())
            ->method('resolveFromHost')
            ->with('kampus-a.siakad.test')
            ->willReturn($tenant);

        $middleware = new ResolveTenant($resolver);

        $request = Request::create('http://kampus-a.siakad.test/dashboard');

        $response = $middleware->handle($request, function ($req) use ($tenant) {
            $this->assertSame($tenant, $req->attributes->get('tenant'));
            return new Response('OK');
        });

        $this->assertSame('OK', $response->getContent());
    }

    public function test_initialize_tenant_middleware_lifecycle(): void
    {
        $tenant = new Tenant();
        $tenant->id = '01900000-0000-7000-8000-000000000001';

        $resolver = $this->createMock(TenantResolver::class);
        $manager = $this->createMock(TenantManager::class);

        $manager->expects($this->once())
            ->method('initialize')
            ->with($tenant);

        $manager->expects($this->once())
            ->method('end');

        $middleware = new InitializeTenant($manager, $resolver);

        $request = Request::create('http://kampus-a.siakad.test/dashboard');
        $request->attributes->set('tenant', $tenant);

        $executed = false;
        $response = $middleware->handle($request, function () use (&$executed) {
            $executed = true;
            return new Response('OK');
        });

        $this->assertTrue($executed);
        $this->assertSame('OK', $response->getContent());
    }

    public function test_prevent_tenant_leakage_terminates_active_context(): void
    {
        $manager = $this->createMock(TenantManager::class);
        $manager->expects($this->once())
            ->method('isInitialized')
            ->willReturn(true);
        $manager->expects($this->once())
            ->method('end');

        $middleware = new PreventTenantLeakage($manager);

        $request = Request::create('http://kampus-a.siakad.test/dashboard');
        $response = new Response('OK');

        $middleware->terminate($request, $response);
    }
}
