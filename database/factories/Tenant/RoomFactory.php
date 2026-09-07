<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\RoomType;
use App\Models\Tenant\Building;
use App\Models\Tenant\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'code' => strtoupper(fake()->unique()->bothify('RK-###')),
            'name' => 'Ruang ' . fake()->numerify('###'),
            'room_type' => RoomType::Classroom,
            'capacity' => 40,
            'status' => 'active',
        ];
    }
}
