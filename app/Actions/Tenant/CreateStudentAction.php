<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateStudentData;
use App\Events\Tenant\StudentCreated;
use App\Models\Tenant\Student;

class CreateStudentAction
{
    public function execute(CreateStudentData $data): Student
    {
        $student = Student::create([
            'user_id' => $data->userId,
            'student_number' => $data->studentNumber,
            'national_student_number' => $data->nationalStudentNumber,
            'study_program_id' => $data->studyProgramId,
            'entry_year' => $data->entryYear,
            'entry_semester_id' => $data->entrySemesterId,
            'admission_type' => $data->admissionType,
            'name' => $data->name,
            'gender' => $data->gender,
            'birth_place' => $data->birthPlace,
            'birth_date' => $data->birthDate,
            'nik' => $data->nik,
            'phone' => $data->phone,
            'email' => $data->email,
            'address' => $data->address,
            'status' => $data->status,
        ]);

        event(new StudentCreated($student));

        return $student;
    }
}
