<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\StudentBillStatus;
use App\Models\Tenant\FeeType;
use App\Models\Tenant\Semester;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudentBill;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentBillFactory extends Factory
{
    protected $model = StudentBill::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'semester_id' => Semester::factory(),
            'fee_type_id' => FeeType::factory(),
            'bill_number' => fake()->unique()->bothify('INV-########'),
            'description' => 'Tagihan Kuliah',
            'amount' => 5000000.00,
            'discount' => 0.00,
            'total_amount' => 5000000.00,
            'due_date' => now()->addMonth()->toDateString(),
            'status' => StudentBillStatus::Issued,
        ];
    }
}
