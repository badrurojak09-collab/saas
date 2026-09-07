<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Course;
use App\Models\Tenant\Curriculum;
use App\Models\Tenant\CurriculumCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurriculumCourseFactory extends Factory
{
    protected $model = CurriculumCourse::class;

    public function definition(): array
    {
        return [
            'curriculum_id' => Curriculum::factory(),
            'course_id' => Course::factory(),
            'semester_number' => fake()->numberBetween(1, 8),
            'course_group' => 'wajib_prodi',
            'is_mandatory' => true,
            'credit_units' => 3.0,
            'minimum_grade' => 'C',
        ];
    }
}
