<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\SemesterType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends TenantModel
{
    protected $table = 'semesters';

    protected $fillable = [
        'academic_year_id',
        'code',
        'name',
        'sequence',
        'semester_type',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sequence' => 'integer',
            'semester_type' => SemesterType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'semester_id');
    }

    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class, 'semester_id');
    }

    public function academicResults(): HasMany
    {
        return $this->hasMany(AcademicResult::class, 'semester_id');
    }

    public function studentBills(): HasMany
    {
        return $this->hasMany(StudentBill::class, 'semester_id');
    }
}
