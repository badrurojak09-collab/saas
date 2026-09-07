<?php

namespace App\Actions\Tenant;

use App\DTOs\Tenant\CreateRoomData;
use App\Models\Tenant\Room;

class UpdateRoomAction
{
    public function execute(Room $room, CreateRoomData $data): Room
    {
        $room->update([
            'building_id' => $data->buildingId,
            'code' => $data->code,
            'name' => $data->name,
            'room_type' => $data->roomType,
            'capacity' => $data->capacity,
            'status' => $data->status,
        ]);

        return $room->fresh();
    }
}
