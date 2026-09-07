<?php

namespace App\Actions\Tenant;

use App\Models\Tenant\CoursePrerequisite;
use InvalidArgumentException;

class AddCoursePrerequisiteAction
{
    public function execute(string $courseId, string $prerequisiteCourseId, string $minimumGrade = 'C'): CoursePrerequisite
    {
        if ($courseId === $prerequisiteCourseId) {
            throw new InvalidArgumentException('Course cannot be its own prerequisite.');
        }

        return CoursePrerequisite::create([
            'course_id' => $courseId,
            'prerequisite_course_id' => $prerequisiteCourseId,
            'minimum_grade' => $minimumGrade,
        ]);
    }
}
