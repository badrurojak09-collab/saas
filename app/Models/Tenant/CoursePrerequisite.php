<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoursePrerequisite extends TenantModel
{
    protected $table = 'course_prerequisites';

    protected $fillable = [
        'course_id',
        'prerequisite_course_id',
        'minimum_grade',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function prerequisiteCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'prerequisite_course_id');
    }
}
