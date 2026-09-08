<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\PerguruanTinggi;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerguruanTinggiFactory extends Factory
{
    protected $model = PerguruanTinggi::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('UNI-##'),
            'name' => fake()->company(),
            'short_name' => fake()->lexify('UNI???'),
            'status' => 'active',
        ];
    }
}
