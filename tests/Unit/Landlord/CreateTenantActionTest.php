<?php

namespace Tests\Unit\Landlord;

use App\Actions\Landlord\CreateTenantAction;
use App\DTOs\Tenant\ProvisionTenantData;
use App\DTOs\Tenant\TenantAdminData;
use App\Enums\Landlord\TenantDatabaseStatus;
use App\Enums\Landlord\TenantStatus;
use App\Events\Landlord\TenantCreated;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateTenantActionTest extends TestCase
{
    private function cleanup(): void
    {
        $codes = ['UNIV_NUSANTARA', 'ITS2026', 'POLITEK'];
        $tenants = Tenant::query()->whereIn('code', $codes)->get();
        foreach ($tenants as $t) {
            $t->domains()->forceDelete();
            $t->database()->forceDelete();
            $t->provisioningJobs()->forceDelete();
            $t->subscriptions()->forceDelete();
            $t->forceDelete();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateTenantAction;
        $this->cleanup();
        Event::fake([TenantCreated::class]);
    }

    protected function tearDown(): void
    {
        $this->cleanup();
        parent::tearDown();
    }

    public function test_creates_tenant_with_pending_status_and_deterministic_database_name(): void
    {
        $tenant = $this->action->execute([
            'name' => 'Universitas Nusantara',
            'code' => 'UNIV_NUSANTARA',
            'slug' => 'univ-nusantara',
        ]);

        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertSame('UNIV_NUSANTARA', $tenant->code);
        $this->assertSame(TenantStatus::PENDING, $tenant->status);

        $database = $tenant->database;
        $this->assertNotNull($database);
        $this->assertSame('siakad_t_univ_nusantara', $database->database);
        $this->assertSame(TenantDatabaseStatus::PROVISIONING, $database->status);
        $this->assertTrue($database->is_primary);

        $domain = $tenant->primaryDomain;
        $this->assertNotNull($domain);
        $this->assertTrue($domain->is_verified);

        Event::assertDispatched(TenantCreated::class, function (TenantCreated $event) use ($tenant) {
            return $event->tenantId === (string) $tenant->getKey();
        });
    }

    public function test_normalizes_special_characters_in_code_for_database_name(): void
    {
        $tenant = $this->action->execute([
            'name' => 'Institut Teknologi & Sains',
            'code' => 'ITS-2026',
            'slug' => 'its-2026',
        ]);

        $this->assertSame('ITS2026', $tenant->code);
        $this->assertSame('siakad_t_its2026', $tenant->database->database);
    }

    public function test_validates_required_fields(): void
    {
        $this->expectException(ValidationException::class);

        $this->action->execute([
            'name' => '',
            'slug' => '',
        ]);
    }

    public function test_supports_provision_tenant_data_dto(): void
    {
        $dto = new ProvisionTenantData(
            name: 'Politeknik Negeri',
            code: 'POLITEK',
            slug: 'politek',
            admin: new TenantAdminData(
                name: 'Super Admin',
                email: 'super@politek.ac.id',
            )
        );

        $tenant = $this->action->execute($dto);

        $this->assertSame('POLITEK', $tenant->code);
        $this->assertSame('siakad_t_politek', $tenant->database->database);

        Event::assertDispatched(TenantCreated::class, function (TenantCreated $event) {
            return $event->adminData !== null && $event->adminData->email === 'super@politek.ac.id';
        });
    }
}
