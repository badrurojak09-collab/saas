<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    protected $model = Building::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('BLD-###')),
            'name' => 'Gedung ' . fake()->unique()->lastName(),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
