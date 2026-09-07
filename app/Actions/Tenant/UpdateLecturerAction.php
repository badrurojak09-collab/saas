<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateLecturerData;
use App\Models\Tenant\Lecturer;

class UpdateLecturerAction
{
    public function execute(Lecturer $lecturer, CreateLecturerData $data): Lecturer
    {
        $lecturer->update([
            'employee_number' => $data->employeeNumber,
            'nidn' => $data->nidn,
            'name' => $data->name,
            'academic_title' => $data->academicTitle,
            'gender' => $data->gender,
            'birth_place' => $data->birthPlace,
            'birth_date' => $data->birthDate,
            'phone' => $data->phone,
            'email' => $data->email,
            'employment_status' => $data->employmentStatus,
            'joined_at' => $data->joinedAt,
            'status' => $data->status,
        ]);

        return $lecturer->fresh();
    }
}
