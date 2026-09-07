<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateBuildingData;
use App\Models\Tenant\Building;

class CreateBuildingAction
{
    public function execute(CreateBuildingData $data): Building
    {
        return Building::create([
            'code' => $data->code,
            'name' => $data->name,
            'description' => $data->description,
            'status' => $data->status,
        ]);
    }
}
