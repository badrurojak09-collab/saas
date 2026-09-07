<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\ClassType;
use App\Models\Tenant\Course;
use App\Models\Tenant\CourseOffering;
use App\Models\Tenant\Curriculum;
use App\Models\Tenant\Semester;
use App\Models\Tenant\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseOfferingFactory extends Factory
{
    protected $model = CourseOffering::class;

    public function definition(): array
    {
        return [
            'semester_id' => Semester::factory(),
            'curriculum_id' => Curriculum::factory(),
            'course_id' => Course::factory(),
            'study_program_id' => StudyProgram::factory(),
            'code' => strtoupper(fake()->unique()->bothify('OFR-####-?')),
            'class_type' => ClassType::Regular,
            'capacity' => 40,
            'status' => 'active',
        ];
    }
}
