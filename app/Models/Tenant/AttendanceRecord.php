<?php

namespace App\Models\Tenant;

use App\Enums\Tenant\AttendanceStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends TenantModel
{
    protected $table = 'attendance_records';

    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'check_in_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'check_in_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
