<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateCourseData;
use App\Events\Tenant\CourseCreated;
use App\Models\Tenant\Course;

class CreateCourseAction
{
    public function execute(CreateCourseData $data): Course
    {
        $course = Course::create([
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

        event(new CourseCreated($course));

        return $course;
    }
}
