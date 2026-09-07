<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\MeetingType;
use App\Models\Tenant\ClassGroup;
use App\Models\Tenant\ClassSchedule;
use App\Models\Tenant\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassScheduleFactory extends Factory
{
    protected $model = ClassSchedule::class;

    public function definition(): array
    {
        return [
            'class_group_id' => ClassGroup::factory(),
            'room_id' => Room::factory(),
            'day_of_week' => fake()->numberBetween(1, 6),
            'start_time' => '08:00',
            'end_time' => '10:30',
            'meeting_type' => MeetingType::Lecture,
            'status' => 'active',
        ];
    }
}
