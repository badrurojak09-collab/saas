<?php

namespace Database\Seeders\Tenant;

use App\Enums\Tenant\DegreeLevel;
use App\Models\Tenant\Department;
use App\Models\Tenant\Faculty;
use App\Models\Tenant\OrganizationUnit;
use App\Models\Tenant\PerguruanTinggi;
use App\Models\Tenant\StudyProgram;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $perguruanTinggi = PerguruanTinggi::query()->updateOrCreate(
            ['code' => 'UABC'],
            [
                'name' => 'Universitas ABC',
                'short_name' => 'UABC',
                'status' => 'active',
            ],
        );

        $facultyUnit = OrganizationUnit::query()->updateOrCreate(
            ['perguruan_tinggi_id' => $perguruanTinggi->id, 'code' => 'FT'],
            ['name' => 'Fakultas Teknik', 'short_name' => 'FT', 'type' => 'faculty', 'status' => 'active'],
        );

        foreach (
            [
                ['code' => 'TI', 'name' => 'Teknik Informatika'],
                ['code' => 'TE', 'name' => 'Teknik Elektro'],
            ] as $studyProgramUnit
        ) {
            OrganizationUnit::query()->updateOrCreate(
                ['perguruan_tinggi_id' => $perguruanTinggi->id, 'code' => $studyProgramUnit['code']],
                [
                    'parent_id' => $facultyUnit->id,
                    'name' => $studyProgramUnit['name'],
                    'short_name' => $studyProgramUnit['code'],
                    'type' => 'study_program',
                    'status' => 'active',
                ],
            );
        }

        foreach (
            [
                ['code' => 'BAAK', 'name' => 'Biro Administrasi Akademik', 'type' => 'bureau'],
                ['code' => 'LPM', 'name' => 'Lembaga Penjaminan Mutu', 'type' => 'quality_unit'],
                ['code' => 'LIB', 'name' => 'Perpustakaan', 'type' => 'library'],
            ] as $administrativeUnit
        ) {
            OrganizationUnit::query()->updateOrCreate(
                ['perguruan_tinggi_id' => $perguruanTinggi->id, 'code' => $administrativeUnit['code']],
                [
                    'name' => $administrativeUnit['name'],
                    'short_name' => $administrativeUnit['code'],
                    'type' => $administrativeUnit['type'],
                    'status' => 'active',
                ],
            );
        }

        $faculty = Faculty::firstOrCreate(
            ['code' => 'FTI'],
            [
                'name' => 'Fakultas Teknologi Informasi',
                'short_name' => 'FTI',
                'description' => 'Fakultas Teknologi Informasi dan Komunikasi',
                'status' => 'active',
            ]
        );

        $department = Department::firstOrCreate(
            ['faculty_id' => $faculty->id, 'code' => 'IF'],
            [
                'name' => 'Departemen Informatika',
                'short_name' => 'IF',
                'status' => 'active',
            ]
        );

        StudyProgram::firstOrCreate(
            ['department_id' => $department->id, 'code' => '55201'],
            [
                'name' => 'Teknik Informatika',
                'degree_level' => DegreeLevel::S1,
                'accreditation_status' => 'Unggul',
                'accreditation_number' => 'SK-001/BAN-PT/2026',
                'accreditation_expired_at' => now()->addYears(5),
                'status' => 'active',
            ]
        );

        StudyProgram::firstOrCreate(
            ['department_id' => $department->id, 'code' => '57201'],
            [
                'name' => 'Sistem Informasi',
                'degree_level' => DegreeLevel::S1,
                'accreditation_status' => 'Baik Sekali',
                'accreditation_number' => 'SK-002/BAN-PT/2026',
                'accreditation_expired_at' => now()->addYears(5),
                'status' => 'active',
            ]
        );
    }
}
