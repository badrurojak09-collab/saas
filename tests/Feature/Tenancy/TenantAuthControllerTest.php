<?php

namespace Tests\Feature\Tenancy;

use App\Enums\Landlord\TenantStatus;
use App\Enums\Tenant\UserStatus;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\User;
use App\Services\Tenant\TenantAuthenticationService;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Contracts\TenantResolver;
use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;

final class TenantAuthControllerTest extends TestCase
{
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = new Tenant;
        $this->tenant->id = '01900000-0000-7000-8000-000000000001';
        $this->tenant->code = 'AUTH_TEST';
        $this->tenant->status = TenantStatus::ACTIVE;

        $resolver = $this->createMock(TenantResolver::class);
        $resolver->method('resolveFromHost')
            ->willReturn($this->tenant);
        $this->app->instance(TenantResolver::class, $resolver);

        $tenantManager = $this->createMock(TenantManager::class);
        $tenantManager->method('isInitialized')->willReturn(true);
        $tenantManager->method('current')->willReturn($this->tenant);
        $tenantManager->method('requireCurrent')->willReturn($this->tenant);
        $this->app->instance(TenantManager::class, $tenantManager);
    }

    public function test_login_page_renders_for_guest(): void
    {
        $response = $this->get('http://auth-test.siakad.test/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke SIAKAD');
    }

    public function test_login_succeeds_and_redirects_to_dashboard(): void
    {
        $authService = $this->createMock(TenantAuthenticationService::class);
        $authService->expects($this->once())
            ->method('attempt')
            ->with('admin@test.com', 'ValidPassword123!', false, $this->anything())
            ->willReturn(true);

        $this->app->instance(TenantAuthenticationService::class, $authService);

        $response = $this->post('http://auth-test.siakad.test/login', [
            'email' => 'admin@test.com',
            'password' => 'ValidPassword123!',
        ]);

        $response->assertRedirect('http://auth-test.siakad.test/dashboard');
    }

    public function test_login_fails_and_returns_error(): void
    {
        $authService = $this->createMock(TenantAuthenticationService::class);
        $authService->expects($this->once())
            ->method('attempt')
            ->willReturn(false);

        $this->app->instance(TenantAuthenticationService::class, $authService);

        $response = $this->from('http://auth-test.siakad.test/login')
            ->post('http://auth-test.siakad.test/login', [
                'email' => 'admin@test.com',
                'password' => 'WrongPassword!',
            ]);

        $response->assertRedirect('http://auth-test.siakad.test/login');
        $response->assertSessionHasErrors('email');
    }

    public function test_logout_redirects_to_login(): void
    {
        $authService = $this->createMock(TenantAuthenticationService::class);
        $authService->expects($this->once())
            ->method('logout');

        $this->app->instance(TenantAuthenticationService::class, $authService);

        $user = new User;
        $user->status = UserStatus::Active;
        $this->actingAs($user, 'tenant');

        $response = $this->post('http://auth-test.siakad.test/logout');

        $response->assertRedirect('http://auth-test.siakad.test/login');
    }

    public function test_dashboard_requires_authenticated_tenant_user(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->withoutExceptionHandling();
        $this->get('http://auth-test.siakad.test/dashboard');
    }

    public function test_dashboard_renders_for_authenticated_tenant_user(): void
    {
        $user = new User;
        $user->name = 'Test User';
        $user->status = UserStatus::Active;
        $this->actingAs($user, 'tenant');

        $response = $this->get('http://auth-test.siakad.test/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat datang, Test User.');
    }
}
