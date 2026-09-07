<?php

namespace Database\Seeders\Tenant;

use App\Enums\Tenant\UserStatus;
use App\Models\Tenant\User;
use App\Support\Tenancy\TenantPermissionContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantAdminSeeder extends Seeder
{
    public function run(): void
    {
        $previous = TenantPermissionContext::enter();

        try {
            $user = User::firstOrCreate(
                ['email' => 'admin@tenant.local'],
                [
                    'name' => 'Administrator SIAKAD',
                    'username' => 'admin',
                    'password' => Hash::make('password'),
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
        } finally {
            TenantPermissionContext::leave($previous);
        }
    }
}
