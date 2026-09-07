<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateSemesterData;
use App\Models\Tenant\Semester;

class CreateSemesterAction
{
    public function execute(CreateSemesterData $data): Semester
    {
        return Semester::create([
            'academic_year_id' => $data->academicYearId,
            'code' => $data->code,
            'name' => $data->name,
            'sequence' => $data->sequence,
            'semester_type' => $data->semesterType,
            'start_date' => $data->startDate,
            'end_date' => $data->endDate,
            'is_active' => $data->isActive,
        ]);
    }
}
