<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\Course;
use App\Models\Tenant\Semester;
use App\Models\Tenant\Transcript;
use App\Models\Tenant\TranscriptItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranscriptItemFactory extends Factory
{
    protected $model = TranscriptItem::class;

    public function definition(): array
    {
        return [
            'transcript_id' => Transcript::factory(),
            'course_id' => Course::factory(),
            'semester_id' => Semester::factory(),
            'credit_units' => 3.0,
            'letter_grade' => 'A',
            'grade_point' => 4.00,
            'quality_points' => 12.00,
        ];
    }
}
