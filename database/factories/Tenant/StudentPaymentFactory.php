<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\StudentPaymentStatus;
use App\Models\Tenant\Student;
use App\Models\Tenant\StudentBill;
use App\Models\Tenant\StudentPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentPaymentFactory extends Factory
{
    protected $model = StudentPayment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'student_bill_id' => StudentBill::factory(),
            'payment_number' => fake()->unique()->bothify('PAY-########'),
            'amount' => 5000000.00,
            'payment_method' => 'bank_transfer',
            'paid_at' => now(),
            'reference_number' => fake()->bothify('REF-########'),
            'status' => StudentPaymentStatus::Settled,
        ];
    }
}
