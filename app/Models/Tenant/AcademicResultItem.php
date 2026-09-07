<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicResultItem extends TenantModel
{
    protected $table = 'academic_result_items';

    protected $fillable = [
        'academic_result_id',
        'course_id',
        'class_group_id',
        'credit_units',
        'numeric_score',
        'letter_grade',
        'grade_point',
        'quality_points',
    ];

    protected function casts(): array
    {
        return [
            'credit_units' => 'decimal:1',
            'numeric_score' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'quality_points' => 'decimal:2',
        ];
    }

    public function academicResult(): BelongsTo
    {
        return $this->belongsTo(AcademicResult::class, 'academic_result_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }
}
