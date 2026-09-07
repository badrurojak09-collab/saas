<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateFacultyData;
use App\Models\Tenant\Faculty;

class CreateFacultyAction
{
    public function execute(CreateFacultyData $data): Faculty
    {
        return Faculty::create([
            'code' => $data->code,
            'name' => $data->name,
            'short_name' => $data->shortName,
            'description' => $data->description,
            'status' => $data->status,
        ]);
    }
}
