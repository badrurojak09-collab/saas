<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\ClassType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseOffering extends TenantModel
{
    use SoftDeletes;

    protected $table = 'course_offerings';

    protected $fillable = [
        'semester_id',
        'curriculum_id',
        'course_id',
        'study_program_id',
        'code',
        'class_type',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'class_type' => ClassType::class,
            'capacity' => 'integer',
        ];
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    public function classGroups(): HasMany
    {
        return $this->hasMany(ClassGroup::class, 'course_offering_id');
    }
}
