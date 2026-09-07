<?php

namespace Tests\Integration\Tenancy;

use App\Actions\Landlord\CreateTenantAction;
use App\Actions\Tenant\AssignRoleAction;
use App\Actions\Tenant\CreateTenantUserAction;
use App\Actions\Tenant\SuspendTenantUserAction;
use App\DTOs\Tenant\CreateTenantUserData;
use App\DTOs\Tenant\ProvisionTenantData;
use App\DTOs\Tenant\TenantAdminData;
use App\DTOs\Tenant\UpdateTenantUserData;
use App\Enums\Landlord\ProvisioningJobStatus;
use App\Enums\Landlord\TenantStatus;
use App\Enums\Tenant\UserStatus;
use App\Enums\Tenant\UserType;
use App\Events\Landlord\TenantCreated;
use App\Events\Landlord\TenantProvisioningCompleted;
use App\Events\Landlord\TenantProvisioningStarted;
use App\Models\Landlord\PlatformUser;
use App\Models\Landlord\ProvisioningJob;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\Tenant\User as TenantUser;
use App\Policies\Tenant\UserPolicy;
use App\Services\Tenant\TenantAuthenticationService;
use App\Services\Tenant\TenantPermissionService;
use App\Services\Tenant\TenantProvisioningService;
use App\Services\Tenant\TenantUserService;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Contracts\TenantManager;
use App\Tenancy\Exceptions\TenantContextMissingException;
use App\Tenancy\Exceptions\TenantInactiveException;
use Filament\Panel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Sprint 06 Acceptance Tests: Tenant Authentication & Spatie Permission Isolation.
 */
class TenantAuthenticationIsolationTest extends TestCase
{
    private string $tempDbPathA;

    private string $tempDbPathB;

    private Tenant $tenantA;

    private Tenant $tenantB;

    private function cleanupTestTenants(): void
    {
        $tenants = Tenant::query()->where('code', 'like', 'AUTH_ISO_%')->get();
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
        Event::fake([
            TenantCreated::class,
            TenantProvisioningStarted::class,
            TenantProvisioningCompleted::class,
        ]);

        $this->tempDbPathA = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_tenant_auth_a_'.uniqid().'.sqlite';
        $this->tempDbPathB = sys_get_temp_dir().DIRECTORY_SEPARATOR.'siakad_tenant_auth_b_'.uniqid().'.sqlite';
        $this->cleanupTestTenants();

        $action = new CreateTenantAction;
        /** @var TenantProvisioningService $service */
        $service = app(TenantProvisioningService::class);

        // 1. Provision Tenant A
        $suffixA = strtoupper(substr(uniqid(), -4));
        $this->tenantA = $action->execute(new ProvisionTenantData(
            name: 'Universitas A Alpha',
            code: 'AUTH_ISO_A_'.$suffixA,
            slug: 'auth-iso-a-'.strtolower($suffixA),
            database: $this->tempDbPathA,
            driver: 'sqlite',
            admin: new TenantAdminData(
                name: 'Initial Admin A',
                email: 'admin@alpha.test',
                username: 'admin_alpha',
                password: 'InitialPassword123!',
            ),
        ));

        $jobA = ProvisioningJob::query()->create([
            'tenant_id' => $this->tenantA->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 1,
        ]);
        $service->provision($this->tenantA, $jobA, attempt: 1);

        // 2. Provision Tenant B
        $suffixB = strtoupper(substr(uniqid(), -4));
        $this->tenantB = $action->execute(new ProvisionTenantData(
            name: 'Universitas B Beta',
            code: 'AUTH_ISO_B_'.$suffixB,
            slug: 'auth-iso-b-'.strtolower($suffixB),
            database: $this->tempDbPathB,
            driver: 'sqlite',
            admin: new TenantAdminData(
                name: 'Initial Admin B',
                email: 'admin@beta.test',
                username: 'admin_beta',
                password: 'InitialPassword123!',
            ),
        ));

        $jobB = ProvisioningJob::query()->create([
            'tenant_id' => $this->tenantB->getKey(),
            'job_type' => 'PROVISION_TENANT',
            'status' => ProvisioningJobStatus::PENDING,
            'attempts' => 1,
        ]);
        $service->provision($this->tenantB, $jobB, attempt: 1);
    }

    protected function tearDown(): void
    {
        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        if ($manager->isInitialized()) {
            $manager->end();
        }

        $this->cleanupTestTenants();

        if (file_exists($this->tempDbPathA)) {
            @unlink($this->tempDbPathA);
        }
        if (file_exists($this->tempDbPathB)) {
            @unlink($this->tempDbPathB);
        }

        parent::tearDown();
    }

    /**
     * Golden Scenario:
     * Tenant A (Alice, tenant_admin) vs Tenant B (Bob, staff).
     * Tests login success, cross-tenant login rejection, session isolation, and permissions.
     */
    public function test_golden_scenario_cross_tenant_authentication_and_authorization(): void
    {
        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        /** @var TenantAuthenticationService $authService */
        $authService = app(TenantAuthenticationService::class);
        /** @var TenantUserService $userService */
        $userService = app(TenantUserService::class);

        // Step 1: Create Alice on Tenant A with role tenant_admin
        $manager->initialize($this->tenantA);

        $alice = $userService->create(new CreateTenantUserData(
            name: 'Alice Wonder',
            email: 'alice@alpha.test',
            username: 'alice',
            password: 'PasswordAlice123!',
            status: UserStatus::Active,
            userType: UserType::Admin,
            roles: ['tenant_admin'],
        ));

        $this->assertSame('alice@alpha.test', $alice->email);
        $this->assertTrue($alice->hasRole('tenant_admin'));
        $this->assertTrue(Hash::check('PasswordAlice123!', $alice->password));
        $this->assertNotSame('PasswordAlice123!', $alice->password); // Never plaintext!

        // Test 1: Alice logs in on Tenant A -> SUCCESS
        $loginAliceSuccess = $authService->attempt('alice@alpha.test', 'PasswordAlice123!');
        $this->assertTrue($loginAliceSuccess);
        $this->assertTrue($authService->check());
        $this->assertSame($alice->id, $authService->user()?->id);
        $this->assertNotNull($alice->fresh()->last_login_at);

        // Step 2: Switch to Tenant B and create Bob with role staff
        $manager->end();
        $manager->initialize($this->tenantB);

        $bob = $userService->create(new CreateTenantUserData(
            name: 'Bob Builder',
            email: 'bob@beta.test',
            username: 'bob',
            password: 'PasswordBob123!',
            status: UserStatus::Active,
            userType: UserType::Staff,
            roles: ['staff'],
        ));

        $this->assertSame('bob@beta.test', $bob->email);
        $this->assertTrue($bob->hasRole('staff'));

        // Test 2: Bob logs in on Tenant B -> SUCCESS
        $loginBobSuccess = $authService->attempt('bob@beta.test', 'PasswordBob123!');
        $this->assertTrue($loginBobSuccess);
        $this->assertTrue($authService->check());
        $this->assertSame($bob->id, $authService->user()?->id);

        // Test 3: Alice attempts login on Tenant B -> DENIED
        $aliceOnB = $authService->attempt('alice@alpha.test', 'PasswordAlice123!');
        $this->assertFalse($aliceOnB);

        // Test 4: Bob attempts login on Tenant A -> DENIED
        $manager->end();
        $manager->initialize($this->tenantA);

        $bobOnA = $authService->attempt('bob@beta.test', 'PasswordBob123!');
        $this->assertFalse($bobOnA);

        // Test 5: Role & Permission Boundary Check
        // Alice on Tenant A has users.delete
        $this->assertTrue($alice->hasPermissionTo('users.delete'));
        $policy = new UserPolicy;
        $otherUser = $userService->create(new CreateTenantUserData(
            name: 'Other User',
            email: 'other@alpha.test',
            username: 'other',
            password: 'PasswordOther123!',
        ));
        $this->assertTrue($policy->delete($alice, $otherUser));

        // Switch to Tenant B: Bob has users.view, but NOT users.delete
        $manager->end();
        $manager->initialize($this->tenantB);
        $this->assertTrue($bob->hasPermissionTo('users.view'));
        $this->assertFalse($bob->hasPermissionTo('users.delete'));
        $targetInB = $userService->create(new CreateTenantUserData(
            name: 'Target in B',
            email: 'target@beta.test',
            username: 'target_b',
            password: 'PasswordTarget123!',
        ));
        $this->assertFalse($policy->delete($bob, $targetInB));

        $manager->end();
    }

    /**
     * Test Suspended Tenant rejects authentication.
     */
    public function test_suspended_tenant_denies_authentication(): void
    {
        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        /** @var TenantAuthenticationService $authService */
        $authService = app(TenantAuthenticationService::class);
        /** @var TenantUserService $userService */
        $userService = app(TenantUserService::class);

        $manager->initialize($this->tenantA);
        $user = $userService->create(new CreateTenantUserData(
            name: 'Charlie',
            email: 'charlie@alpha.test',
            password: 'CharliePassword123!',
            status: UserStatus::Active,
        ));
        $manager->end();

        // Suspend Tenant A
        $this->tenantA->update(['status' => TenantStatus::SUSPENDED]);

        // Attempting to initialize suspended tenant throws TenantInactiveException
        $this->expectException(TenantInactiveException::class);
        $manager->initialize($this->tenantA);

        // Authentication cannot proceed
        $authService->attempt('charlie@alpha.test', 'CharliePassword123!');
    }

    /**
     * Test Suspended and Inactive users are rejected from authentication.
     */
    public function test_suspended_and_inactive_users_are_rejected(): void
    {
        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        /** @var TenantAuthenticationService $authService */
        $authService = app(TenantAuthenticationService::class);
        /** @var TenantUserService $userService */
        $userService = app(TenantUserService::class);

        $manager->initialize($this->tenantA);

        $suspendedUser = $userService->create(new CreateTenantUserData(
            name: 'Suspended User',
            email: 'suspended@alpha.test',
            password: 'Password123!',
            status: UserStatus::Suspended,
        ));

        $inactiveUser = $userService->create(new CreateTenantUserData(
            name: 'Inactive User',
            email: 'inactive@alpha.test',
            password: 'Password123!',
            status: UserStatus::Inactive,
        ));

        $pendingUser = $userService->create(new CreateTenantUserData(
            name: 'Pending User',
            email: 'pending@alpha.test',
            password: 'Password123!',
            status: UserStatus::Pending,
        ));

        $this->assertFalse($authService->attempt('suspended@alpha.test', 'Password123!'));
        $this->assertFalse($authService->attempt('inactive@alpha.test', 'Password123!'));
        $this->assertFalse($authService->attempt('pending@alpha.test', 'Password123!'));

        // Activate user and verify login succeeds
        $userService->activate($suspendedUser);
        $this->assertTrue($authService->attempt('suspended@alpha.test', 'Password123!'));

        // Suspend user again and verify login rejected
        $userService->suspend($suspendedUser);
        $this->assertFalse($authService->attempt('suspended@alpha.test', 'Password123!'));

        $manager->end();
    }

    /**
     * Invariant: Missing Tenant Context throws TenantContextMissingException on auth.
     */
    public function test_auth_without_tenant_context_throws_exception(): void
    {
        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        if ($manager->isInitialized()) {
            $manager->end();
        }

        /** @var TenantAuthenticationService $authService */
        $authService = app(TenantAuthenticationService::class);

        $this->expectException(TenantContextMissingException::class);
        $authService->attempt('any@user.test', 'password');
    }

    /**
     * Test Rate Limiting is isolated per tenant and does not block other tenants.
     */
    public function test_rate_limiting_is_isolated_per_tenant(): void
    {
        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        /** @var TenantAuthenticationService $authService */
        $authService = app(TenantAuthenticationService::class);
        /** @var TenantUserService $userService */
        $userService = app(TenantUserService::class);

        $manager->initialize($this->tenantA);
        $userA = $userService->create(new CreateTenantUserData(
            name: 'User A',
            email: 'usera@alpha.test',
            password: 'ValidPassword123!',
        ));

        // Attempt 5 failed logins on Tenant A
        for ($i = 0; $i < 5; $i++) {
            $result = $authService->attempt('usera@alpha.test', 'WrongPassword!');
            $this->assertFalse($result);
        }

        // 6th attempt on Tenant A should throw ValidationException (Throttled)
        $throttled = false;
        try {
            $authService->attempt('usera@alpha.test', 'ValidPassword123!');
        } catch (ValidationException $e) {
            $throttled = true;
        }
        $this->assertTrue($throttled, 'Expected 6th attempt on Tenant A to be throttled.');

        // Now switch to Tenant B with same IP and login name: should NOT be throttled!
        $manager->end();
        $manager->initialize($this->tenantB);

        $userB = $userService->create(new CreateTenantUserData(
            name: 'User B',
            email: 'usera@alpha.test', // Same login string on Tenant B
            password: 'ValidPassword123!',
        ));

        // Tenant B authentication should succeed without being blocked by Tenant A's throttle
        $successOnB = $authService->attempt('usera@alpha.test', 'ValidPassword123!');
        $this->assertTrue($successOnB, 'Tenant B should not be throttled by Tenant A brute force.');

        $manager->end();
    }

    /**
     * Test Spatie Permission Cache Key isolation between tenants.
     */
    public function test_permission_cache_is_isolated_per_tenant(): void
    {
        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        $registrar = app(PermissionRegistrar::class);

        $manager->initialize($this->tenantA);
        $this->assertSame('tenant:'.$this->tenantA->id.':permission-cache', $registrar->cacheKey);
        $manager->end();

        $manager->initialize($this->tenantB);
        $this->assertSame('tenant:'.$this->tenantB->id.':permission-cache', $registrar->cacheKey);
        $manager->end();
    }

    /**
     * Test Architecture Invariants: Connection isolation and Filament panel contract.
     */
    public function test_architecture_connection_and_filament_access(): void
    {
        $tenantUser = new TenantUser();
        $this->assertSame('tenant', $tenantUser->getConnectionName());

        $tenantRole = new Role();
        $this->assertSame('tenant', $tenantRole->getConnectionName());

        $tenantPermission = new Permission();
        $this->assertSame('tenant', $tenantPermission->getConnectionName());

        $platformUser = new PlatformUser();
        $this->assertSame('landlord', $platformUser->getConnectionName());

        // Test Filament canAccessPanel
        $tenantUser->status = UserStatus::Active;
        $tenantPanel = (new Panel())->id('tenant');
        $platformPanel = (new Panel())->id('platform');

        $this->assertTrue($tenantUser->canAccessPanel($tenantPanel));
        $this->assertFalse($tenantUser->canAccessPanel($platformPanel));

        $tenantUser->status = UserStatus::Suspended;
        $this->assertFalse($tenantUser->canAccessPanel($tenantPanel));
    }
}
