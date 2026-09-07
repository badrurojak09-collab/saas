<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateStudentData;
use App\Models\Tenant\Student;

class UpdateStudentAction
{
    public function execute(Student $student, CreateStudentData $data): Student
    {
        $student->update([
            'study_program_id' => $data->studyProgramId,
            'name' => $data->name,
            'gender' => $data->gender,
            'birth_place' => $data->birthPlace,
            'birth_date' => $data->birthDate,
            'nik' => $data->nik,
            'phone' => $data->phone,
            'email' => $data->email,
            'address' => $data->address,
        ]);

        return $student->fresh();
    }
}
