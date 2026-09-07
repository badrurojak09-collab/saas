<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\MeetingType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSchedule extends TenantModel
{
    protected $table = 'class_schedules';

    protected $fillable = [
        'class_group_id',
        'room_id',
        'day_of_week',
        'start_time',
        'end_time',
        'meeting_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'meeting_type' => MeetingType::class,
        ];
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
