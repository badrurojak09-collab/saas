<?php

namespace Tests\Integration\Tenancy;

use App\Models\Landlord\PlatformUser;
use App\Models\Tenant\User as TenantUser;
use Tests\TestCase;

class TenantAuthenticationTest extends TestCase
{
    public function test_auth_guards_and_providers_are_isolated(): void
    {
        // 1. Tenant guard configuration
        $this->assertArrayHasKey('tenant', config('auth.guards'));
        $this->assertSame('session', config('auth.guards.tenant.driver'));
        $this->assertSame('tenant_users', config('auth.guards.tenant.provider'));

        // 2. Tenant user provider configuration
        $this->assertArrayHasKey('tenant_users', config('auth.providers'));
        $this->assertSame('eloquent', config('auth.providers.tenant_users.driver'));
        $this->assertSame(TenantUser::class, config('auth.providers.tenant_users.model'));

        // 3. Platform guard configuration
        $this->assertArrayHasKey('platform', config('auth.guards'));
        $this->assertSame('session', config('auth.guards.platform.driver'));
        $this->assertSame('platform_users', config('auth.guards.platform.provider'));

        // 4. Platform user provider configuration
        $this->assertArrayHasKey('platform_users', config('auth.providers'));
        $this->assertSame('eloquent', config('auth.providers.platform_users.driver'));
        $this->assertSame(PlatformUser::class, config('auth.providers.platform_users.model'));
    }

    public function test_tenant_user_uses_tenant_connection(): void
    {
        $user = new TenantUser();
        $this->assertSame('tenant', $user->getConnectionName());
    }

    public function test_platform_user_uses_landlord_connection(): void
    {
        $platformUser = new PlatformUser();
        $this->assertSame('landlord', $platformUser->getConnectionName());
    }
}
