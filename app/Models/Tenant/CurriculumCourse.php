<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumCourse extends TenantModel
{
    protected $table = 'curriculum_courses';

    protected $fillable = [
        'curriculum_id',
        'course_id',
        'semester_number',
        'course_group',
        'is_mandatory',
        'credit_units',
        'minimum_grade',
    ];

    protected function casts(): array
    {
        return [
            'semester_number' => 'integer',
            'is_mandatory' => 'boolean',
            'credit_units' => 'decimal:1',
        ];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
