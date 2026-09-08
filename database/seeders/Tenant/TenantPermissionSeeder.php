<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Permission;
use App\Support\Tenancy\TenantPermissionContext;
use Illuminate\Database\Seeder;

class TenantPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $previous = TenantPermissionContext::enter();

        try {
            $permissions = [
                // Identity & Users
                'users.view',
                'users.create',
                'users.update',
                'users.edit',
                'users.delete',
                'users.activate',
                'users.suspend',

                // Roles & Permissions
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.manage',
                'roles.delete',
                'permissions.view',
                'permissions.assign',

                // Organization
                'organization.view',
                'organization.manage',
                'organization_units.view',
                'organization_units.create',
                'organization_units.update',
                'organization_units.delete',
                'organization_units.activate',
                'organization_units.deactivate',
                'organization_memberships.view',
                'organization_memberships.create',
                'organization_memberships.update',
                'organization_memberships.delete',

                // Academic Master
                'academic.view',
                'academic.manage',
                'academic_years.manage',
                'semesters.manage',
                'courses.manage',
                'curriculums.manage',

                // People
                'lecturers.view',
                'lecturers.manage',
                'students.view',
                'students.manage',

                // Scheduling
                'scheduling.view',
                'scheduling.manage',

                // KRS & Attendance
                'krs.view',
                'krs.manage',
                'attendance.view',
                'attendance.manage',

                // Assessment & Grades
                'grades.view',
                'grades.input',
                'grades.publish',
                'grades.lock',

                // Finance
                'finance.view',
                'finance.manage',

                // Documents
                'documents.view',
                'documents.manage',

                // Audit & PDDIKTI
                'audit.view',
                'pddikti.view',
                'pddikti.sync',
            ];

            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => 'tenant'],
                    ['name' => $permissionName, 'guard_name' => 'tenant']
                );
            }
        } finally {
            TenantPermissionContext::leave($previous);
        }
    }
}
