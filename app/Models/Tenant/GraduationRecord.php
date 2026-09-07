<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\GraduationStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduationRecord extends TenantModel
{
    protected $table = 'graduation_records';

    protected $fillable = [
        'student_id',
        'study_program_id',
        'graduation_number',
        'graduation_date',
        'graduation_period',
        'final_gpa',
        'total_credits',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'graduation_date' => 'date',
            'final_gpa' => 'decimal:2',
            'total_credits' => 'decimal:1',
            'status' => GraduationStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
