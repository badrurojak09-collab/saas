<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\GradeStatus;
use App\Models\Tenant\ClassGroup;
use App\Models\Tenant\FinalGrade;
use App\Models\Tenant\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinalGradeFactory extends Factory
{
    protected $model = FinalGrade::class;

    public function definition(): array
    {
        return [
            'class_group_id' => ClassGroup::factory(),
            'student_id' => Student::factory(),
            'numeric_score' => 85.00,
            'letter_grade' => 'A',
            'grade_point' => 4.00,
            'status' => GradeStatus::Published,
            'published_at' => now(),
        ];
    }
}
