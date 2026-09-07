<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassGroup extends TenantModel
{
    use SoftDeletes;

    protected $table = 'class_groups';

    protected $fillable = [
        'course_offering_id',
        'code',
        'name',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(ClassLecturer::class, 'class_group_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'class_group_id');
    }

    public function studyPlanItems(): HasMany
    {
        return $this->hasMany(StudyPlanItem::class, 'class_group_id');
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'class_group_id');
    }

    public function assessmentComponents(): HasMany
    {
        return $this->hasMany(AssessmentComponent::class, 'class_group_id');
    }

    public function finalGrades(): HasMany
    {
        return $this->hasMany(FinalGrade::class, 'class_group_id');
    }
}
