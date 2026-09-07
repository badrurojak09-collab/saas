<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Department;
use App\Models\Tenant\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'faculty_id' => Faculty::factory(),
            'code' => strtoupper(fake()->unique()->bothify('DEP-###')),
            'name' => 'Departemen ' . fake()->unique()->words(2, true),
            'short_name' => strtoupper(fake()->lexify('D??')),
            'status' => 'active',
        ];
    }
}
