<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\AttendanceSession;
use App\Models\Tenant\ClassGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceSessionFactory extends Factory
{
    protected $model = AttendanceSession::class;

    public function definition(): array
    {
        return [
            'class_group_id' => ClassGroup::factory(),
            'meeting_number' => fake()->numberBetween(1, 16),
            'meeting_date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'topic' => 'Pengenalan Materi ' . fake()->words(2, true),
            'status' => 'open',
        ];
    }
}
