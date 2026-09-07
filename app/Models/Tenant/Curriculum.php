<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curriculum extends TenantModel
{
    use SoftDeletes;

    protected $table = 'curriculums';

    protected $fillable = [
        'study_program_id',
        'code',
        'name',
        'description',
        'effective_start_year',
        'effective_end_year',
        'status',
    ];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
    }

    public function curriculumCourses(): HasMany
    {
        return $this->hasMany(CurriculumCourse::class, 'curriculum_id');
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'curriculum_id');
    }
}
