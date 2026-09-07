<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\AcademicResultStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicResult extends TenantModel
{
    protected $table = 'academic_results';

    protected $fillable = [
        'student_id',
        'semester_id',
        'total_credits',
        'total_quality_points',
        'semester_gpa',
        'cumulative_gpa',
        'status',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'total_credits' => 'decimal:1',
            'total_quality_points' => 'decimal:2',
            'semester_gpa' => 'decimal:2',
            'cumulative_gpa' => 'decimal:2',
            'status' => AcademicResultStatus::class,
            'generated_at' => 'datetime',
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

    public function items(): HasMany
    {
        return $this->hasMany(AcademicResultItem::class, 'academic_result_id');
    }
}
