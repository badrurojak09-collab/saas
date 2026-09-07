<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Curriculum;
use App\Models\Tenant\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurriculumFactory extends Factory
{
    protected $model = Curriculum::class;

    public function definition(): array
    {
        return [
            'study_program_id' => StudyProgram::factory(),
            'code' => strtoupper(fake()->unique()->bothify('KUR-####-?')),
            'name' => 'Kurikulum ' . fake()->words(2, true) . ' 2026',
            'description' => fake()->sentence(),
            'effective_start_year' => 2026,
            'effective_end_year' => 2030,
            'status' => 'active',
        ];
    }
}
