<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Support\Tenancy\TenantPermissionContext;
use Illuminate\Database\Seeder;

class TenantRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantPermissionSeeder::class,
            TenantRoleSeeder::class,
        ]);

        $previous = TenantPermissionContext::enter();

        try {
            $akademik = Role::where('name', 'akademik')->where('guard_name', 'tenant')->first();
            if ($akademik) {
                $akademik->givePermissionTo([
                    'academic.view', 'academic.manage',
                    'courses.manage', 'curriculums.manage',
                    'lecturers.view', 'students.view', 'students.manage',
                    'scheduling.view', 'scheduling.manage',
                    'krs.view', 'krs.manage',
                    'attendance.view', 'attendance.manage',
                    'grades.view', 'grades.publish', 'grades.lock',
                ]);
            }

            $kaprodi = Role::where('name', 'kaprodi')->where('guard_name', 'tenant')->first();
            if ($kaprodi) {
                $kaprodi->givePermissionTo([
                    'organization.view',
                    'academic.view',
                    'courses.manage', 'curriculums.manage',
                    'lecturers.view', 'students.view',
                    'scheduling.view', 'scheduling.manage',
                    'krs.view', 'krs.manage',
                    'grades.view',
                ]);
            }

            $dosen = Role::where('name', 'dosen')->where('guard_name', 'tenant')->first();
            if ($dosen) {
                $dosen->givePermissionTo([
                    'academic.view',
                    'scheduling.view',
                    'attendance.view', 'attendance.manage',
                    'grades.view', 'grades.input',
                ]);
            }

            $mahasiswa = Role::where('name', 'mahasiswa')->where('guard_name', 'tenant')->first();
            if ($mahasiswa) {
                $mahasiswa->givePermissionTo([
                    'academic.view',
                    'scheduling.view',
                    'krs.view',
                    'attendance.view',
                    'grades.view',
                    'documents.view',
                ]);
            }

            $keuangan = Role::where('name', 'keuangan')->where('guard_name', 'tenant')->first();
            if ($keuangan) {
                $keuangan->givePermissionTo([
                    'finance.view', 'finance.manage',
                    'students.view',
                ]);
            }

            $operator = Role::where('name', 'operator')->where('guard_name', 'tenant')->first();
            if ($operator) {
                $operator->givePermissionTo([
                    'academic.view',
                    'students.view',
                    'documents.view', 'documents.manage',
                    'pddikti.view',
                ]);
            }
        } finally {
            TenantPermissionContext::leave($previous);
        }
    }
}
