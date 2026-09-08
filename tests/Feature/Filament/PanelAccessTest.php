<?php

namespace Tests\Feature\Filament;

use App\Models\Landlord\PlatformUser;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\User as TenantUser;
use App\Tenancy\Contracts\TenantManager;
use Database\Seeders\LocalDevelopmentSeeder;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LocalDevelopmentSeeder::class);
    }

    public function test_landlord_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');
        $response->assertSuccessful();
    }

    public function test_authenticated_platform_admin_can_access_landlord_dashboard(): void
    {
        /** @var PlatformUser $admin */
        $admin = PlatformUser::where('email', 'admin@siakad.test')->first();

        $response = $this->actingAs($admin, 'platform')->get('/admin');
        $response->assertSuccessful();
    }

    public function test_tenant_login_page_renders_successfully(): void
    {
        $response = $this->get('/tenant/login?tenant=demo');
        $response->assertSuccessful();
    }

    public function test_authenticated_tenant_admin_can_access_tenant_dashboard(): void
    {
        /** @var Tenant $tenant */
        $tenant = Tenant::where('code', 'DEMO')->first();

        /** @var TenantManager $tenantManager */
        $tenantManager = app(TenantManager::class);
        $tenantManager->initialize($tenant);

        try {
            /** @var TenantUser $user */
            $user = TenantUser::where('email', 'staff@demo.test')->first();

            $response = $this->actingAs($user, 'tenant')->get('/tenant?tenant=demo');
            $response->assertSuccessful();

            $studentsResponse = $this->actingAs($user, 'tenant')->get('/tenant/students?tenant=demo');
            $studentsResponse->assertSuccessful();
        } finally {
            $tenantManager->end();
        }
    }

    public function test_tenant_dashboard_loads_user_from_session_without_preinitialized_context(): void
    {
        /** @var Tenant $tenant */
        $tenant = Tenant::where('code', 'DEMO')->first();

        /** @var TenantManager $tenantManager */
        $tenantManager = app(TenantManager::class);
        $tenantManager->initialize($tenant);

        try {
            /** @var TenantUser $user */
            $user = TenantUser::where('email', 'staff@demo.test')->first();
            $this->assertNotNull($user);

            Auth::guard('tenant')->login($user);
        } finally {
            $tenantManager->end();
        }

        Auth::forgetGuards();

        $response = $this->get('/tenant?tenant=demo');

        $response->assertSuccessful();
        $response->assertSee('Ringkasan tenant');
    }
}
