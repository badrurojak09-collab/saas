<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateFacultyData;
use App\Models\Tenant\Faculty;

class UpdateFacultyAction
{
    public function execute(Faculty $faculty, CreateFacultyData $data): Faculty
    {
        $faculty->update([
            'code' => $data->code,
            'name' => $data->name,
            'short_name' => $data->shortName,
            'description' => $data->description,
            'status' => $data->status,
        ]);

        return $faculty->fresh();
    }
}
