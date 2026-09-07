<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateStudyProgramData;
use App\Models\Tenant\StudyProgram;

class CreateStudyProgramAction
{
    public function execute(CreateStudyProgramData $data): StudyProgram
    {
        return StudyProgram::create([
            'department_id' => $data->departmentId,
            'code' => $data->code,
            'name' => $data->name,
            'degree_level' => $data->degreeLevel,
            'accreditation_status' => $data->accreditationStatus,
            'accreditation_number' => $data->accreditationNumber,
            'accreditation_expired_at' => $data->accreditationExpiredAt,
            'status' => $data->status,
        ]);
    }
}
