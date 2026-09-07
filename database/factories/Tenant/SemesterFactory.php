<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\SemesterType;
use App\Models\Tenant\AcademicYear;
use App\Models\Tenant\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'code' => strtoupper(fake()->unique()->bothify('SEM-####-?')),
            'name' => 'Semester Ganjil ' . fake()->year(),
            'sequence' => 1,
            'semester_type' => SemesterType::Odd,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->addMonths(6)->endOfMonth()->toDateString(),
            'is_active' => true,
        ];
    }
}
