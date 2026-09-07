<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateUserData;
use App\Events\Tenant\UserCreated;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserAction
{
    public function execute(CreateUserData $data): User
    {
        return DB::connection('tenant')->transaction(function () use ($data) {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'username' => $data->username ?? explode('@', $data->email)[0],
                'password' => $data->password ? Hash::make($data->password) : Hash::make(Str::random(16)),
                'status' => $data->status,
                'metadata' => $data->metadata,
            ]);

            if (! empty($data->roles)) {
                $previous = TenantPermissionContext::enter();
                try {
                    $user->syncRoles($data->roles);
                } finally {
                    TenantPermissionContext::leave($previous);
                }
            }

            event(new UserCreated($user));

            return $user;
        });
    }
}
