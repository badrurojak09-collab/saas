<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\AssessmentComponent;
use App\Models\Tenant\AssessmentScore;
use App\Models\Tenant\Student;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentScoreFactory extends Factory
{
    protected $model = AssessmentScore::class;

    public function definition(): array
    {
        return [
            'assessment_component_id' => AssessmentComponent::factory(),
            'student_id' => Student::factory(),
            'score' => 85.50,
            'graded_by' => User::factory(),
            'graded_at' => now(),
            'notes' => null,
        ];
    }
}
