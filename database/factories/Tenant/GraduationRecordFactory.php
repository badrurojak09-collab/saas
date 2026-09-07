<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\GraduationStatus;
use App\Models\Tenant\GraduationRecord;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudyProgram;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GraduationRecordFactory extends Factory
{
    protected $model = GraduationRecord::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'study_program_id' => StudyProgram::factory(),
            'graduation_number' => fake()->unique()->bothify('YUD-####/2026'),
            'graduation_date' => now()->toDateString(),
            'graduation_period' => '2026/2027-1',
            'final_gpa' => 3.75,
            'total_credits' => 144.0,
            'status' => GraduationStatus::Approved,
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ];
    }
}
