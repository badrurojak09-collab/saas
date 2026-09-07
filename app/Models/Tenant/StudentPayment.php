<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\StudentPaymentStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPayment extends TenantModel
{
    protected $table = 'student_payments';

    protected $fillable = [
        'student_id',
        'student_bill_id',
        'payment_number',
        'amount',
        'payment_method',
        'paid_at',
        'reference_number',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'status' => StudentPaymentStatus::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(StudentBill::class, 'student_bill_id');
    }
}
