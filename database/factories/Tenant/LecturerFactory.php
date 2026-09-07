<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\EmploymentStatus;
use App\Models\Tenant\Lecturer;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LecturerFactory extends Factory
{
    protected $model = Lecturer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'employee_number' => fake()->unique()->numerify('EMP########'),
            'nidn' => fake()->unique()->numerify('00##########'),
            'name' => fake()->name(),
            'academic_title' => 'M.Kom.',
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date('Y-m-d', '-30 years'),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'employment_status' => EmploymentStatus::Permanent,
            'joined_at' => fake()->date('Y-m-d', '-5 years'),
            'status' => 'active',
        ];
    }
}
