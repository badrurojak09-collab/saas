<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\AcademicResultStatus;
use App\Models\Tenant\AcademicResult;
use App\Models\Tenant\Semester;
use App\Models\Tenant\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicResultFactory extends Factory
{
    protected $model = AcademicResult::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'semester_id' => Semester::factory(),
            'total_credits' => 20.0,
            'total_quality_points' => 75.0,
            'semester_gpa' => 3.75,
            'cumulative_gpa' => 3.75,
            'status' => AcademicResultStatus::Approved,
            'generated_at' => now(),
        ];
    }
}
