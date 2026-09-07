<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\StudentStatus;
use App\Models\Tenant\Semester;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudyProgram;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'student_number' => fake()->unique()->numerify('NIM########'),
            'national_student_number' => fake()->unique()->numerify('NISN##########'),
            'study_program_id' => StudyProgram::factory(),
            'entry_year' => 2026,
            'entry_semester_id' => Semester::factory(),
            'admission_type' => 'mandiri',
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date('Y-m-d', '-20 years'),
            'nik' => fake()->numerify('320############'),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'status' => StudentStatus::Active,
            'graduation_date' => null,
        ];
    }
}
