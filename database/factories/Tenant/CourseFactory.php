<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\CourseCategory;
use App\Enums\Tenant\CourseType;
use App\Enums\Tenant\GradingType;
use App\Models\Tenant\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('MK-###')),
            'name' => fake()->unique()->words(3, true),
            'short_name' => strtoupper(fake()->lexify('MK??')),
            'description' => fake()->sentence(),
            'credit_units' => fake()->randomElement([2.0, 3.0, 4.0]),
            'course_type' => CourseType::Mandatory,
            'course_category' => CourseCategory::General,
            'grading_type' => GradingType::StandardLetter,
            'status' => 'active',
        ];
    }
}
