<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateDepartmentData;
use App\Models\Tenant\Department;

class CreateDepartmentAction
{
    public function execute(CreateDepartmentData $data): Department
    {
        return Department::create([
            'faculty_id' => $data->facultyId,
            'code' => $data->code,
            'name' => $data->name,
            'short_name' => $data->shortName,
            'status' => $data->status,
        ]);
    }
}
