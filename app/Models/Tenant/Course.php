<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\CourseCategory;
use App\Enums\Tenant\CourseType;
use App\Enums\Tenant\GradingType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends TenantModel
{
    use SoftDeletes;

    protected $table = 'courses';

    protected $fillable = [
        'code',
        'name',
        'short_name',
        'description',
        'credit_units',
        'course_type',
        'course_category',
        'grading_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'credit_units' => 'decimal:1',
            'course_type' => CourseType::class,
            'course_category' => CourseCategory::class,
            'grading_type' => GradingType::class,
        ];
    }

    public function curriculumCourses(): HasMany
    {
        return $this->hasMany(CurriculumCourse::class, 'course_id');
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'course_id');
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'course_id',
            'prerequisite_course_id'
        )->withPivot('minimum_grade');
    }
}
