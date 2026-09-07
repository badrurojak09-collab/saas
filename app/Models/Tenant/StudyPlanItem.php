<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyPlanItem extends TenantModel
{
    protected $table = 'study_plan_items';

    protected $fillable = [
        'study_plan_id',
        'class_group_id',
        'course_id',
        'credit_units',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'credit_units' => 'decimal:1',
        ];
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class, 'study_plan_id');
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
