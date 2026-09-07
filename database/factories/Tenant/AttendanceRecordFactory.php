<?php

namespace Database\Factories\Tenant;

use App\Enums\Tenant\AttendanceStatus;
use App\Models\Tenant\AttendanceRecord;
use App\Models\Tenant\AttendanceSession;
use App\Models\Tenant\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'attendance_session_id' => AttendanceSession::factory(),
            'student_id' => Student::factory(),
            'status' => AttendanceStatus::Present,
            'check_in_at' => now(),
            'notes' => null,
        ];
    }
}
