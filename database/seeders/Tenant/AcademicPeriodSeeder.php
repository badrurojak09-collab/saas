<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\AcademicYear;
use App\Models\Tenant\Semester;
use App\Enums\Tenant\SemesterType;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::firstOrCreate(
            ['code' => '2026/2027'],
            [
                'name' => 'Tahun Akademik 2026/2027',
                'start_date' => '2026-09-01',
                'end_date' => '2027-08-31',
                'status' => 'active',
            ]
        );

        Semester::firstOrCreate(
            [
                'academic_year_id' => $year->id,
                'code' => '20261',
            ],
            [
                'name' => 'Semester Ganjil 2026/2027',
                'sequence' => 1,
                'semester_type' => SemesterType::Odd,
                'start_date' => '2026-09-01',
                'end_date' => '2027-01-31',
                'is_active' => true,
            ]
        );

        Semester::firstOrCreate(
            [
                'academic_year_id' => $year->id,
                'code' => '20262',
            ],
            [
                'name' => 'Semester Genap 2026/2027',
                'sequence' => 2,
                'semester_type' => SemesterType::Even,
                'start_date' => '2027-02-01',
                'end_date' => '2027-06-30',
                'is_active' => false,
            ]
        );
    }
}
