<?php

namespace Database\Seeders\Tenant;

use App\Enums\Tenant\DegreeLevel;
use App\Models\Tenant\Department;
use App\Models\Tenant\Faculty;
use App\Models\Tenant\StudyProgram;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
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
