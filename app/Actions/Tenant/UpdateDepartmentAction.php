<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateDepartmentData;
use App\Models\Tenant\Department;

class UpdateDepartmentAction
{
    public function execute(Department $department, CreateDepartmentData $data): Department
    {
        $department->update([
            'faculty_id' => $data->facultyId,
            'code' => $data->code,
            'name' => $data->name,
            'short_name' => $data->shortName,
            'status' => $data->status,
        ]);

        return $department->fresh();
    }
}
