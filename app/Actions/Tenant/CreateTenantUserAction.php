<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateTenantUserData;
use App\Events\Tenant\TenantRoleAssigned;
use App\Events\Tenant\TenantUserCreated;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use App\Tenancy\Contracts\TenantManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenantUserAction
{
    public function __construct(
        protected TenantManager $tenantManager,
    ) {}

    public function execute(CreateTenantUserData $data, ?string $performedBy = null): User
    {
        $tenantId = (string) $this->tenantManager->requireCurrent()->getKey();

        return DB::connection('tenant')->transaction(function () use ($data, $tenantId, $performedBy) {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'username' => $data->username,
                'password' => $data->password ? Hash::make($data->password) : Hash::make(Str::random(16)),
                'phone' => $data->phone,
                'avatar_path' => $data->avatarPath,
                'status' => $data->status,
                'user_type' => $data->userType,
                'metadata' => $data->metadata,
            ]);

            if (! empty($data->roles)) {
                $previous = TenantPermissionContext::enter($tenantId);
                try {
                    $user->syncRoles($data->roles);
                } finally {
                    TenantPermissionContext::leave($previous);
                }

                event(new TenantRoleAssigned($tenantId, (string) $user->id, $data->roles, $performedBy));
            }

            event(new TenantUserCreated($tenantId, (string) $user->id, $performedBy));

            return $user;
        });
    }
}
