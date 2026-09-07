<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\ClassGroup;
use App\Models\Tenant\Course;
use App\Models\Tenant\StudyPlan;
use App\Models\Tenant\StudyPlanItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyPlanItemFactory extends Factory
{
    protected $model = StudyPlanItem::class;

    public function definition(): array
    {
        return [
            'study_plan_id' => StudyPlan::factory(),
            'class_group_id' => ClassGroup::factory(),
            'course_id' => Course::factory(),
            'credit_units' => 3.0,
            'status' => 'approved',
        ];
    }
}
