<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\TranscriptStatus;
use App\Models\Tenant\Student;
use App\Models\Tenant\Transcript;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranscriptFactory extends Factory
{
    protected $model = Transcript::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'transcript_number' => fake()->unique()->bothify('TR-####/UNIV/2026'),
            'generated_at' => now(),
            'total_credits' => 144.0,
            'final_gpa' => 3.75,
            'status' => TranscriptStatus::Issued,
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ];
    }
}
