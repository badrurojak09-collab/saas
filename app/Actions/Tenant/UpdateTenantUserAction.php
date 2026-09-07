<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\UpdateTenantUserData;
use App\Events\Tenant\TenantRoleAssigned;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Contracts\TenantManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateTenantUserAction
{
    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    public function execute(User $user, UpdateTenantUserData $data, ?string $performedBy = null): User
    {
        $tenantId = (string) $this->tenantManager->requireCurrent()->getKey();

        return DB::connection('tenant')->transaction(function () use ($user, $data, $tenantId, $performedBy) {
            $updates = array_filter([
                'name' => $data->name,
                'email' => $data->email,
                'username' => $data->username,
                'phone' => $data->phone,
                'avatar_path' => $data->avatarPath,
                'status' => $data->status,
                'user_type' => $data->userType,
                'metadata' => $data->metadata,
            ], fn ($v) => $v !== null);

            if (! empty($data->password)) {
                $updates['password'] = Hash::make($data->password);
            }

            $user->update($updates);

            if ($data->roles !== null) {
                $previous = TenantPermissionContext::enter($tenantId);
                try {
                    $user->syncRoles($data->roles);
                } finally {
                    TenantPermissionContext::leave($previous);
                }

                event(new TenantRoleAssigned($tenantId, (string) $user->id, $data->roles, $performedBy));
            }

            return $user->fresh();
        });
    }
}
