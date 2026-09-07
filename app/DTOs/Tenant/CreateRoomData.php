<?php

namespace App\DTOs\Tenant;

use App\Enums\Tenant\RoomType;

readonly class CreateRoomData
{
    public function __construct(
        public string $buildingId,
        public string $code,
        public string $name,
        public RoomType $roomType = RoomType::Classroom,
        public int $capacity = 40,
        public string $status = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            buildingId: $data['building_id'],
            code: $data['code'],
            name: $data['name'],
            roomType: isset($data['room_type'])
                ? ($data['room_type'] instanceof RoomType ? $data['room_type'] : RoomType::from($data['room_type']))
                : RoomType::Classroom,
            capacity: (int) ($data['capacity'] ?? 40),
            status: $data['status'] ?? 'active',
        );
    }
}
