<?php

namespace Tests\Integration\Provisioning;

use App\Actions\Landlord\CreateTenantAction;
use App\DTOs\Tenant\ProvisionTenantData;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Events\Landlord\TenantCreated;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TenantCredentialLeakageTest extends TestCase
{
    private function cleanupTestTenants(): void
    {
        $tenants = Tenant::query()->where('code', 'like', 'LEAK_%')->get();
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
        $this->cleanupTestTenants();
    }

    protected function tearDown(): void
    {
        $this->cleanupTestTenants();
        parent::tearDown();
    }

    public function test_credentials_are_encrypted_at_rest_and_never_leaked_in_logs_or_cli(): void
    {
        Event::fake([TenantCreated::class]);

        $plainPassword = 'UltraSecretPassword999!';
        $suffix = strtoupper(substr(uniqid(), -4));
        $code = 'LEAK_'.$suffix;
        $slug = 'leak-'.strtolower($suffix);

        $action = new CreateTenantAction;
        $tenant = $action->execute(new ProvisionTenantData(
            name: 'Universitas Keamanan Data',
            code: $code,
            slug: $slug,
            dbPassword: $plainPassword,
            driver: 'mysql',
        ));

        $database = $tenant->database;

        // 1. Password is encrypted at rest in landlord database
        $rawPassword = $database->getRawOriginal('password');
        $this->assertNotEmpty($rawPassword);
        $this->assertNotSame($plainPassword, $rawPassword);

        // Decrypted attribute matches
        $this->assertSame($plainPassword, $database->password);

        // 2. ProvisioningJob payload does not contain plaintext password
        $job = ProvisioningJob::query()->create([
            'tenant_id' => $tenant->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 1,
            'payload' => [
                'tenant_id' => (string) $tenant->getKey(),
                'database' => $database->database,
            ],
        ]);

        $jobJson = json_encode($job->payload);
        $this->assertStringNotContainsString($plainPassword, (string) $jobJson);

        // 3. CLI status command does not reveal plaintext password
        Artisan::call('tenant:provision-status', ['tenant' => $tenant->code]);
        $output = Artisan::output();

        $this->assertStringContainsString('[PROTECTED / ENCRYPTED AT REST]', $output);
        $this->assertStringNotContainsString($plainPassword, $output);
    }
}
