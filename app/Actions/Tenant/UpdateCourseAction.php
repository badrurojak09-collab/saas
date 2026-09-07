<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateCourseData;
use App\Models\Tenant\Course;

class UpdateCourseAction
{
    public function execute(Course $course, CreateCourseData $data): Course
    {
        $course->update([
            'code' => $data->code,
            'name' => $data->name,
            'short_name' => $data->shortName,
            'description' => $data->description,
            'credit_units' => $data->creditUnits,
            'course_type' => $data->courseType,
            'course_category' => $data->courseCategory,
            'grading_type' => $data->gradingType,
            'status' => $data->status,
        ]);

        return $course->fresh();
    }
}
