<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\GradeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalGrade extends TenantModel
{
    protected $table = 'final_grades';

    protected $fillable = [
        'class_group_id',
        'student_id',
        'numeric_score',
        'letter_grade',
        'grade_point',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'numeric_score' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'status' => GradeStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function isLocked(): bool
    {
        return $this->status === GradeStatus::Locked;
    }
}
