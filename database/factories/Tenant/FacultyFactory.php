<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyFactory extends Factory
{
    protected $model = Faculty::class;

    public function definition(): array
    {
        $name = 'Fakultas ' . fake()->unique()->words(2, true);
        return [
            'code' => strtoupper(fake()->unique()->bothify('F-###')),
            'name' => $name,
            'short_name' => strtoupper(fake()->lexify('F??')),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
