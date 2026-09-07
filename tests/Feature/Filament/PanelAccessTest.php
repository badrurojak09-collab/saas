<?php

namespace Tests\Feature\Filament;

use App\Enums\Landlord\PlatformUserStatus;
use App\Enums\Tenant\UserStatus;
use App\Models\Landlord\PlatformUser;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\User as TenantUser;
use App\Tenancy\Contracts\TenantManager;
use Database\Seeders\LocalDevelopmentSeeder;
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
}
