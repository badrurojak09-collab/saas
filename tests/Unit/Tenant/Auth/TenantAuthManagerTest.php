<?php

namespace Tests\Unit\Tenant\Auth;

use App\Auth\Tenant\TenantAuthManager;
use App\Models\Tenant\User;
use App\Services\Tenant\TenantAuthenticationService;
use App\Tenancy\Contracts\TenantManager;
use Illuminate\Contracts\Auth\Guard;
use Tests\TestCase;

final class TenantAuthManagerTest extends TestCase
{
    public function test_guard_returns_tenant_guard(): void
    {
        $authService = $this->createMock(TenantAuthenticationService::class);
        $tenantManager = $this->createMock(TenantManager::class);

        $authManager = new TenantAuthManager($authService, $tenantManager);

        $guard = $authManager->guard();
        $this->assertInstanceOf(Guard::class, $guard);
    }

    public function test_user_and_id_and_check_delegates_to_auth_service(): void
    {
        $authService = $this->createMock(TenantAuthenticationService::class);
        $tenantManager = $this->createMock(TenantManager::class);

        $user = new User;
        $user->id = 'user-uuid-123';

        $authService->expects($this->exactly(2))
            ->method('user')
            ->willReturn($user);

        $authService->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $authManager = new TenantAuthManager($authService, $tenantManager);

        $this->assertSame($user, $authManager->user());
        $this->assertSame('user-uuid-123', $authManager->id());
        $this->assertTrue($authManager->check());
    }

    public function test_id_returns_null_when_no_authenticated_user(): void
    {
        $authService = $this->createMock(TenantAuthenticationService::class);
        $tenantManager = $this->createMock(TenantManager::class);

        $authService->expects($this->once())
            ->method('user')
            ->willReturn(null);

        $authManager = new TenantAuthManager($authService, $tenantManager);

        $this->assertNull($authManager->id());
    }
}
