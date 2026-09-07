<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\ClassGroup;
use App\Models\Tenant\ClassLecturer;
use App\Models\Tenant\Lecturer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassLecturerFactory extends Factory
{
    protected $model = ClassLecturer::class;

    public function definition(): array
    {
        return [
            'class_group_id' => ClassGroup::factory(),
            'lecturer_id' => Lecturer::factory(),
            'role' => 'primary',
        ];
    }
}
