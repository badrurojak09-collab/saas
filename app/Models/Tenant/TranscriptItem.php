<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranscriptItem extends TenantModel
{
    protected $table = 'transcript_items';

    protected $fillable = [
        'transcript_id',
        'course_id',
        'semester_id',
        'credit_units',
        'letter_grade',
        'grade_point',
        'quality_points',
    ];

    protected function casts(): array
    {
        return [
            'credit_units' => 'decimal:1',
            'grade_point' => 'decimal:2',
            'quality_points' => 'decimal:2',
        ];
    }

    public function transcript(): BelongsTo
    {
        return $this->belongsTo(Transcript::class, 'transcript_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
