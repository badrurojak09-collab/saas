<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\AcademicResult;
use App\Models\Tenant\AcademicResultItem;
use App\Models\Tenant\ClassGroup;
use App\Models\Tenant\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicResultItemFactory extends Factory
{
    protected $model = AcademicResultItem::class;

    public function definition(): array
    {
        return [
            'academic_result_id' => AcademicResult::factory(),
            'course_id' => Course::factory(),
            'class_group_id' => ClassGroup::factory(),
            'credit_units' => 3.0,
            'numeric_score' => 85.00,
            'letter_grade' => 'A',
            'grade_point' => 4.00,
            'quality_points' => 12.00,
        ];
    }
}
