<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\StudentBillStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentBill extends TenantModel
{
    protected $table = 'student_bills';

    protected $fillable = [
        'student_id',
        'semester_id',
        'fee_type_id',
        'bill_number',
        'description',
        'amount',
        'discount',
        'total_amount',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'due_date' => 'date',
            'status' => StudentBillStatus::class,
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class, 'student_bill_id');
    }
}
