<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\ClassGroup;
use App\Models\Tenant\CourseOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassGroupFactory extends Factory
{
    protected $model = ClassGroup::class;

    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'code' => strtoupper(fake()->unique()->bothify('GRP-?')),
            'name' => 'Kelas ' . strtoupper(fake()->lexify('?')),
            'capacity' => 40,
            'status' => 'active',
        ];
    }
}
