<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\AddCurriculumCourseData;
use App\Models\Tenant\CurriculumCourse;

class AddCurriculumCourseAction
{
    public function execute(AddCurriculumCourseData $data): CurriculumCourse
    {
        return CurriculumCourse::create([
            'curriculum_id' => $data->curriculumId,
            'course_id' => $data->courseId,
            'semester_number' => $data->semesterNumber,
            'course_group' => $data->courseGroup,
            'is_mandatory' => $data->isMandatory,
            'credit_units' => $data->creditUnits,
            'minimum_grade' => $data->minimumGrade,
        ]);
    }
}
