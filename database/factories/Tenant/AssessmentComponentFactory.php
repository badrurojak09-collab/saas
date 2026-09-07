<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\AssessmentType;
use App\Models\Tenant\AssessmentComponent;
use App\Models\Tenant\ClassGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentComponentFactory extends Factory
{
    protected $model = AssessmentComponent::class;

    public function definition(): array
    {
        return [
            'class_group_id' => ClassGroup::factory(),
            'code' => strtoupper(fake()->unique()->bothify('COMP-###')),
            'name' => 'Tugas ' . fake()->words(2, true),
            'weight' => 20.00,
            'max_score' => 100.00,
            'assessment_type' => AssessmentType::Assignment,
            'sequence' => 1,
        ];
    }
}
