<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\UpdateUserData;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function execute(User $user, UpdateUserData $data): User
    {
        return DB::connection('tenant')->transaction(function () use ($user, $data) {
            $updates = array_filter([
                'name' => $data->name,
                'email' => $data->email,
                'username' => $data->username,
                'status' => $data->status,
                'metadata' => $data->metadata,
            ], fn ($v) => ! is_null($v));

            if (! empty($data->password)) {
                $updates['password'] = Hash::make($data->password);
            }

            if (! empty($updates)) {
                $user->update($updates);
            }

            if (! is_null($data->roles)) {
                $previous = TenantPermissionContext::enter();
                try {
                    $user->syncRoles($data->roles);
                } finally {
                    TenantPermissionContext::leave($previous);
                }
            }

            return $user->fresh();
        });
    }
}
