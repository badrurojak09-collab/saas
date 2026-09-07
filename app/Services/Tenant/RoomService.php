<?php

namespace App\Services\Tenant;

use App\Actions\Tenant\CreateBuildingAction;
use App\Actions\Tenant\CreateRoomAction;
use App\Actions\Tenant\UpdateRoomAction;
use App\DTOs\Tenant\CreateBuildingData;
use App\DTOs\Tenant\CreateRoomData;
use App\Models\Tenant\Building;
use App\Models\Tenant\Room;
use Illuminate\Database\Eloquent\Collection;

class RoomService
{
    public function __construct(
        protected CreateBuildingAction $createBuildingAction,
        protected CreateRoomAction $createRoomAction,
        protected UpdateRoomAction $updateRoomAction,
    ) {}

    public function allBuildings(): Collection
    {
        return Building::with('rooms')->get();
    }

    public function createBuilding(CreateBuildingData $data): Building
    {
        return $this->createBuildingAction->execute($data);
    }

    public function createRoom(CreateRoomData $data): Room
    {
        return $this->createRoomAction->execute($data);
    }

    public function updateRoom(Room $room, CreateRoomData $data): Room
    {
        return $this->updateRoomAction->execute($room, $data);
    }
}
