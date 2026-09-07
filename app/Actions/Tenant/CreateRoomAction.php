<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateRoomData;
use App\Models\Tenant\Room;

class CreateRoomAction
{
    public function execute(CreateRoomData $data): Room
    {
        return Room::create([
            'building_id' => $data->buildingId,
            'code' => $data->code,
            'name' => $data->name,
            'room_type' => $data->roomType,
            'capacity' => $data->capacity,
            'status' => $data->status,
        ]);
    }
}
