<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\OrganizationUnit;
use App\Models\Tenant\PerguruanTinggi;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationUnitFactory extends Factory
{
    protected $model = OrganizationUnit::class;

    public function definition(): array
    {
        return [
            'perguruan_tinggi_id' => PerguruanTinggi::factory(),
            'code' => fake()->unique()->bothify('ORG-##'),
            'name' => fake()->company(),
            'short_name' => fake()->lexify('ORG???'),
            'type' => 'other',
            'status' => 'active',
            'sort_order' => 0,
        ];
    }
}
