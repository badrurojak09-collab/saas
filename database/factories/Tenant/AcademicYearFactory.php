<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $startYear = fake()->unique()->numberBetween(2020, 2040);
        $endYear = $startYear + 1;
        $code = "{$startYear}/{$endYear}";

        return [
            'code' => $code,
            'name' => "Tahun Akademik {$code}",
            'start_date' => "{$startYear}-09-01",
            'end_date' => "{$endYear}-08-31",
            'status' => 'active',
        ];
    }
}
