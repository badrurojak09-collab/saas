<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\FeeBillingFrequency;
use App\Models\Tenant\FeeType;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeeTypeFactory extends Factory
{
    protected $model = FeeType::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('BIAYA-###')),
            'name' => 'Biaya ' . fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'default_amount' => 5000000.00,
            'billing_frequency' => FeeBillingFrequency::Semester,
            'status' => 'active',
        ];
    }
}
