<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends TenantModel
{
    protected $table = 'attendance_sessions';

    protected $fillable = [
        'class_group_id',
        'meeting_number',
        'meeting_date',
        'start_time',
        'end_time',
        'topic',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'meeting_number' => 'integer',
            'meeting_date' => 'date',
        ];
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'attendance_session_id');
    }
}
