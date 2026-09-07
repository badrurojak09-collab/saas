<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\DegreeLevel;
use App\Models\Tenant\Department;
use App\Models\Tenant\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyProgramFactory extends Factory
{
    protected $model = StudyProgram::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'code' => strtoupper(fake()->unique()->bothify('PRODI-###')),
            'name' => 'Program Studi ' . fake()->unique()->words(2, true),
            'degree_level' => DegreeLevel::S1,
            'accreditation_status' => 'Unggul',
            'accreditation_number' => fake()->bothify('SK-####/BAN-PT/2026'),
            'accreditation_expired_at' => now()->addYears(5),
            'status' => 'active',
        ];
    }
}
