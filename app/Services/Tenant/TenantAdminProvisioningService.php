<?php

namespace App\Services\Tenant;

use App\DTOs\Tenant\TenantAdminData;
use App\Enums\Tenant\UserStatus;
use App\Models\Landlord\TenantDatabase;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Connection\TenantConnectionManager;
use App\Tenancy\Exceptions\Provisioning\TenantAdminProvisioningException;
use Illuminate\Support\Facades\Hash;
use Throwable;

class TenantAdminProvisioningService
{
    public function __construct(
        protected TenantConnectionManager $connectionManager,
    ) {}

    /**
     * Provision the initial administrator account on the tenant database.
     *
     * @throws TenantAdminProvisioningException
     */
    public function provision(TenantDatabase $database, ?TenantAdminData $adminData = null): User
    {
        $this->connectionManager->configureForProvisioning($database);
        $data = $adminData ?? new TenantAdminData;

        $previousContext = TenantPermissionContext::enter();

        try {
            /** @var User $user */
            $user = User::query()->updateOrCreate(
                ['email' => $data->email],
                [
                    'name' => $data->name,
                    'username' => $data->username ?: 'admin',
                    'password' => Hash::make($data->password ?: 'password'),
                    'status' => UserStatus::Active,
                    'email_verified_at' => now(),
                ]
            );

            if (! $user->hasRole('tenant_admin')) {
                $user->assignRole('tenant_admin');
            }

            if (! $user->hasRole('super_admin')) {
                $user->assignRole('super_admin');
            }

            return $user;
        } catch (Throwable $e) {
            throw new TenantAdminProvisioningException(
                sprintf('Failed to provision tenant admin [%s]: %s', $data->email, $e->getMessage()),
                $e
            );
        } finally {
            TenantPermissionContext::leave($previousContext);
        }
    }
}
