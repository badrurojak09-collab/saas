<?php

namespace Tests\Unit\Tenant\Middleware;

use App\Enums\Landlord\TenantStatus;
use App\Enums\Tenant\UserStatus;
use App\Http\Middleware\AuthenticateTenant;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\User;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Exceptions\TenantContextMissingException;
use App\Tenancy\Exceptions\TenantInactiveException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

final class AuthenticateTenantTest extends TestCase
{
    public function test_throws_exception_when_tenant_not_initialized(): void
    {
        $tenantManager = $this->createMock(TenantManager::class);
        $tenantManager->expects($this->once())
            ->method('isInitialized')
            ->willReturn(false);

        $middleware = new AuthenticateTenant($tenantManager);
        $request = Request::create('http://kampus.test/dashboard');

        $this->expectException(TenantContextMissingException::class);
        $middleware->handle($request, fn () => new Response('OK'));
    }

    public function test_throws_exception_when_tenant_is_inactive(): void
    {
        $tenant = new Tenant;
        $tenant->code = 'INACTIVE_TENANT';
        $tenant->status = TenantStatus::SUSPENDED;

        $tenantManager = $this->createMock(TenantManager::class);
        $tenantManager->expects($this->once())
            ->method('isInitialized')
            ->willReturn(true);
        $tenantManager->expects($this->once())
            ->method('requireCurrent')
            ->willReturn($tenant);

        $middleware = new AuthenticateTenant($tenantManager);
        $request = Request::create('http://kampus.test/dashboard');

        $this->expectException(TenantInactiveException::class);
        $middleware->handle($request, fn () => new Response('OK'));
    }

    public function test_throws_authentication_exception_when_user_not_authenticated(): void
    {
        $tenant = new Tenant;
        $tenant->code = 'ACTIVE_TENANT';
        $tenant->status = TenantStatus::ACTIVE;

        $tenantManager = $this->createMock(TenantManager::class);
        $tenantManager->expects($this->once())
            ->method('isInitialized')
            ->willReturn(true);
        $tenantManager->expects($this->once())
            ->method('requireCurrent')
            ->willReturn($tenant);

        Auth::guard('tenant')->logout();

        $middleware = new AuthenticateTenant($tenantManager);
        $request = Request::create('http://kampus.test/dashboard');

        $this->expectException(AuthenticationException::class);
        $middleware->handle($request, fn () => new Response('OK'));
    }

    public function test_aborts_403_when_authenticated_user_is_not_active(): void
    {
        $tenant = new Tenant;
        $tenant->code = 'ACTIVE_TENANT';
        $tenant->status = TenantStatus::ACTIVE;

        $tenantManager = $this->createMock(TenantManager::class);
        $tenantManager->expects($this->once())
            ->method('isInitialized')
            ->willReturn(true);
        $tenantManager->expects($this->once())
            ->method('requireCurrent')
            ->willReturn($tenant);

        $user = new User;
        $user->status = UserStatus::Suspended;

        Auth::guard('tenant')->setUser($user);

        $middleware = new AuthenticateTenant($tenantManager);
        $request = Request::create('http://kampus.test/dashboard');

        $this->expectException(HttpException::class);
        try {
            $middleware->handle($request, fn () => new Response('OK'));
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            $this->assertFalse(Auth::guard('tenant')->check());
            throw $e;
        }
    }

    public function test_passes_when_tenant_and_user_are_active(): void
    {
        $tenant = new Tenant;
        $tenant->code = 'ACTIVE_TENANT';
        $tenant->status = TenantStatus::ACTIVE;

        $tenantManager = $this->createMock(TenantManager::class);
        $tenantManager->expects($this->once())
            ->method('isInitialized')
            ->willReturn(true);
        $tenantManager->expects($this->once())
            ->method('requireCurrent')
            ->willReturn($tenant);

        $user = new User;
        $user->status = UserStatus::Active;

        Auth::guard('tenant')->setUser($user);

        $middleware = new AuthenticateTenant($tenantManager);
        $request = Request::create('http://kampus.test/dashboard');

        $response = $middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame('OK', $response->getContent());
    }
}
