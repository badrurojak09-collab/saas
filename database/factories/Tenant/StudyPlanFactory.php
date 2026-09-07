<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\StudyPlanStatus;
use App\Models\Tenant\Semester;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudyPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyPlanFactory extends Factory
{
    protected $model = StudyPlan::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'semester_id' => Semester::factory(),
            'status' => StudyPlanStatus::Submitted,
            'submitted_at' => now(),
            'approved_at' => null,
            'approved_by' => null,
            'notes' => fake()->sentence(),
        ];
    }
}
