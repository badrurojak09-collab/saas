<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Support\Tenancy\TenantPermissionContext;
use Illuminate\Database\Seeder;

class TenantRoleSeeder extends Seeder
{
    public function run(): void
    {
        $previous = TenantPermissionContext::enter();

        try {
            $roles = [
                'tenant_admin',
                'super_admin',
                'admin',
                'staff',
                'akademik',
                'kaprodi',
                'dosen',
                'mahasiswa',
                'keuangan',
                'operator',
            ];

            foreach ($roles as $roleName) {
                Role::firstOrCreate(
                    ['name' => $roleName, 'guard_name' => 'tenant'],
                    ['name' => $roleName, 'guard_name' => 'tenant']
                );
            }

            // Assign all permissions to tenant_admin and super_admin
            $tenantAdmin = Role::where('name', 'tenant_admin')->where('guard_name', 'tenant')->first();
            if ($tenantAdmin) {
                $tenantAdmin->syncPermissions(Permission::where('guard_name', 'tenant')->get());
            }

            $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'tenant')->first();
            if ($superAdmin) {
                $superAdmin->syncPermissions(Permission::where('guard_name', 'tenant')->get());
            }

            // Assign admin permissions
            $admin = Role::where('name', 'admin')->where('guard_name', 'tenant')->first();
            if ($admin) {
                $admin->syncPermissions([
                    'users.view', 'users.create', 'users.update', 'users.edit',
                    'organization.view', 'organization.manage',
                    'academic.view', 'academic.manage',
                    'academic_years.manage', 'semesters.manage',
                    'courses.manage', 'curriculums.manage',
                    'lecturers.view', 'lecturers.manage',
                    'students.view', 'students.manage',
                    'scheduling.view', 'scheduling.manage',
                    'documents.view', 'documents.manage',
                ]);
            }

            // Staff permissions (view only for users and identity)
            $staff = Role::where('name', 'staff')->where('guard_name', 'tenant')->first();
            if ($staff) {
                $staff->syncPermissions([
                    'users.view',
                    'organization.view',
                    'academic.view',
                    'students.view',
                    'documents.view',
                ]);
            }
        } finally {
            TenantPermissionContext::leave($previous);
        }
    }
}
