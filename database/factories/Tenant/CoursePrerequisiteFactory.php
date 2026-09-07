<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Course;
use App\Models\Tenant\CoursePrerequisite;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoursePrerequisiteFactory extends Factory
{
    protected $model = CoursePrerequisite::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'prerequisite_course_id' => Course::factory(),
            'minimum_grade' => 'C',
        ];
    }
}
