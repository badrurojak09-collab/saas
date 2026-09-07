<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\RoomType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends TenantModel
{
    use SoftDeletes;

    protected $table = 'rooms';

    protected $fillable = [
        'building_id',
        'code',
        'name',
        'room_type',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'room_type' => RoomType::class,
            'capacity' => 'integer',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'room_id');
    }
}
